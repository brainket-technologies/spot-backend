<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For active restaurants (e.g. Brown Cafe), ensure schedule entries exist for all days (0 to 6)
        $restaurants = DB::table('restaurants')->where('status', 1)->pluck('id');
        foreach ($restaurants as $restaurantId) {
            for ($day = 0; $day <= 6; $day++) {
                $exists = DB::table('restaurant_schedule')
                    ->where('restaurant_id', $restaurantId)
                    ->where('day', $day)
                    ->exists();

                if (!$exists) {
                    DB::table('restaurant_schedule')->insert([
                        'restaurant_id' => $restaurantId,
                        'day' => $day,
                        'opening_time' => '00:00:00',
                        'closing_time' => '23:59:00',
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
    }
};
