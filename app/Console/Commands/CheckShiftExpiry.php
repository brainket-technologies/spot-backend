<?php

namespace App\Console\Commands;

use App\CentralLogics\Helpers;
use App\Models\DeliveryMan;
use Illuminate\Console\Command;

class CheckShiftExpiry extends Command
{
    protected $signature = 'deliveryman:check-shift-expiry';
    protected $description = 'Automatically change delivery boy status to offline when their shift has ended';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $activeDms = DeliveryMan::where('active', 1)
            ->whereNotNull('shift_id')
            ->with(['dm_shift'])
            ->get();

        $count = 0;
        foreach ($activeDms as $dm) {
            if (Helpers::check_and_expire_dm_shift($dm)) {
                $count++;
            }
        }

        $this->info("Checked {$activeDms->count()} active delivery men. Auto-offlined {$count} expired shifts.");
        return 0;
    }
}
