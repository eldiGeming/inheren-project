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
        // Nomor HP Sales & PIC, mendampingi kolom nama masing-masing
        Schema::table('clients', function (Blueprint $table) {
            $table->string('sales_phone', 30)->nullable()->after('sales_name');
            $table->string('pic_phone', 30)->nullable()->after('pic_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['sales_phone', 'pic_phone']);
        });
    }
};
