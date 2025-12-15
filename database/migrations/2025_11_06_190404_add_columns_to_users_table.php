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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone',11)->nullable()->after('email'); 
            $table->foreignId('pharmacy_id')->constrained('pharmacies')->OnDelete('cascade');
            $table->enum('role',['vendor','client']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
           $table->dropColumn('phone');
           $table->dropColumn('role');
        });
    }
};
