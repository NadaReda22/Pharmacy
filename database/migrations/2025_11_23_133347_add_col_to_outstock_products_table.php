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
        Schema::table('outstock_products', function (Blueprint $table) {
            $table->boolean('notified')->default(false);
            $table->unique(['user_id','product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outstock_products', function (Blueprint $table) {
            $table->dropColumn(['notified','product_id']);
        });
    }
};
