<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('add_ons') && !Schema::hasColumn('add_ons', 'addon_category_id')) {
            Schema::table('add_ons', function (Blueprint $table) {
                $table->unsignedBigInteger('addon_category_id')->nullable()->after('restaurant_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('add_ons') && Schema::hasColumn('add_ons', 'addon_category_id')) {
            Schema::table('add_ons', function (Blueprint $table) {
                $table->dropColumn('addon_category_id');
            });
        }
    }
};
