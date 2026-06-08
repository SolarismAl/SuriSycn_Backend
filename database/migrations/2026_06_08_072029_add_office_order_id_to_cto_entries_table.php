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
        Schema::table('cto_entries', function (Blueprint $table) {
            $table->uuid('office_order_id')->nullable()->after('status');
            $table->foreign('office_order_id')->references('id')->on('office_orders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cto_entries', function (Blueprint $table) {
            $table->dropForeign(['office_order_id']);
            $table->dropColumn('office_order_id');
        });
    }
};
