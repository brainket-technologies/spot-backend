<?php

namespace App\Console\Commands;

use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoCancelUnacceptedOrders extends Command
{
    protected $signature = 'order:auto-cancel-unaccepted';
    protected $description = 'Auto-cancel orders not accepted by restaurant or delivery boy within 10 minutes with wallet refund for online/wallet payments';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $tenMinutesAgo = Carbon::now()->subMinutes(10);
        $fiveMinutesAgoStart = Carbon::now()->subMinutes(6);
        $fiveMinutesAgoEnd = Carbon::now()->subMinutes(5);

        // -----------------------------------------------------------------
        // 1. Send 5-Minute Alert/Reminder to Restaurant for pending orders
        // -----------------------------------------------------------------
        try {
            $pendingOrdersForAlert = Order::with(['restaurant.vendor'])
                ->where('order_status', 'pending')
                ->where('scheduled', 0)
                ->whereBetween('created_at', [$fiveMinutesAgoStart, $fiveMinutesAgoEnd])
                ->get();

            foreach ($pendingOrdersForAlert as $order) {
                if ($order->restaurant && $order->restaurant->vendor && $order->restaurant->vendor->firebase_token) {
                    $alertData = [
                        'title' => translate('messages.order_push_title'),
                        'description' => "Reminder: Order #{$order->id} is still pending. Please accept or reject.",
                        'order_id' => $order->id,
                        'image' => '',
                        'type' => 'order_reminder',
                    ];
                    Helpers::send_push_notif_to_device($order->restaurant->vendor->firebase_token, $alertData);
                }
            }
        } catch (\Exception $e) {
            info(["5-min alert error: " . $e->getMessage()]);
        }

        // -----------------------------------------------------------------
        // 2. Auto-Cancel: Restaurant did not accept within 10 minutes
        // -----------------------------------------------------------------
        $unacceptedByRestaurant = Order::with(['details', 'restaurant.vendor', 'customer'])
            ->where('order_status', 'pending')
            ->where('scheduled', 0)
            ->where('created_at', '<=', $tenMinutesAgo)
            ->get();

        $restaurantCancelCount = 0;
        foreach ($unacceptedByRestaurant as $order) {
            $this->cancelOrderWithRefund($order, 'Restaurant did not accept order in time');
            $restaurantCancelCount++;
        }

        // -----------------------------------------------------------------
        // 3. Auto-Cancel: No Delivery Boy accepted within 10 minutes
        // (Order confirmed by restaurant, but delivery_man_id is still null)
        // -----------------------------------------------------------------
        $unacceptedByDeliveryman = Order::with(['details', 'restaurant.vendor', 'customer'])
            ->where('order_status', 'confirmed')
            ->where('order_type', 'delivery')
            ->whereNull('delivery_man_id')
            ->where('scheduled', 0)
            ->where(function ($query) use ($tenMinutesAgo) {
                $query->where('confirmed', '<=', $tenMinutesAgo)
                      ->orWhere(function ($q) use ($tenMinutesAgo) {
                          $q->whereNull('confirmed')->where('created_at', '<=', $tenMinutesAgo);
                      });
            })
            ->get();

        $deliveryCancelCount = 0;
        foreach ($unacceptedByDeliveryman as $order) {
            $this->cancelOrderWithRefund($order, 'No delivery partner available');
            $deliveryCancelCount++;
        }

        $this->info("Auto-cancelled: {$restaurantCancelCount} orders unaccepted by restaurant, {$deliveryCancelCount} orders unaccepted by delivery boy.");
        return 0;
    }

    private function cancelOrderWithRefund($order, string $reason)
    {
        try {
            DB::beginTransaction();

            $order->order_status = 'canceled';
            $order->canceled = now();
            $order->canceled_by = 'system';
            $order->cancellation_reason = $reason;
            $order->save();

            // Decrease sell count & restore restaurant subscription order count
            Helpers::decreaseSellCount(order_details: $order->details);
            Helpers::increment_order_count($order->restaurant);

            // ---------------------------------------------------------
            // Refund Rule:
            // - If COD: NO REFUND (cash was not collected)
            // - If Online / Wallet: REFUND TO CUSTOMER WALLET
            // ---------------------------------------------------------
            $isRefundable = in_array($order->payment_method, ['digital_payment', 'wallet'])
                            || ($order->payment_status == 'paid' && $order->payment_method != 'cash_on_delivery');

            $refunded = false;
            $refundAmount = (float) $order->order_amount;

            if ($isRefundable && $refundAmount > 0 && $order->user_id && $order->is_guest == 0) {
                CustomerLogic::create_wallet_transaction(
                    user_id: $order->user_id,
                    amount: $refundAmount,
                    transaction_type: 'order_refund',
                    referance: $order->id
                );
                $refunded = true;
            }

            DB::commit();

            // ---------------------------------------------------------
            // Send Notifications
            // ---------------------------------------------------------
            // Customer notification
            if ($order->customer && $order->customer->cm_firebase_token) {
                $customerMsg = $refunded
                    ? "Your order #{$order->id} has been cancelled ({$reason}). ₹{$refundAmount} has been refunded to your wallet."
                    : "Your order #{$order->id} has been cancelled ({$reason}).";

                $custNotif = [
                    'title' => 'Order Cancelled',
                    'description' => $customerMsg,
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status',
                    'order_status' => 'canceled',
                ];
                Helpers::send_push_notif_to_device($order->customer->cm_firebase_token, $custNotif);

                DB::table('user_notifications')->insert([
                    'data' => json_encode($custNotif),
                    'user_id' => $order->user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Restaurant notification
            if ($order->restaurant && $order->restaurant->vendor && $order->restaurant->vendor->firebase_token) {
                $restNotif = [
                    'title' => 'Order Cancelled',
                    'description' => "Order #{$order->id} was cancelled by system: {$reason}.",
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status',
                    'order_status' => 'canceled',
                ];
                Helpers::send_push_notif_to_device($order->restaurant->vendor->firebase_token, $restNotif);
            }

            // Fire standard notification handler
            Helpers::send_order_notification($order);

        } catch (\Exception $e) {
            DB::rollBack();
            info(["Auto-cancel order #{$order->id} failed: " . $e->getMessage()]);
        }
    }
}
