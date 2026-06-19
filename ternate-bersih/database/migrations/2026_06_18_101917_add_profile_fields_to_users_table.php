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
            $table->string('nik', 16)->unique()->nullable()->after('id');
            $table->string('phone_number', 15)->nullable()->after('email');
            $table->text('address')->nullable()->after('phone_number');
            $table->foreignId('village_id')->nullable()->after('address')->constrained('villages')->nullOnDelete();
            $table->enum('role', ['Administrator', 'Operator DLH', 'Koordinator Lapangan', 'Petugas Lapangan', 'Driver Armada', 'Masyarakat'])->default('Masyarakat')->after('village_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
