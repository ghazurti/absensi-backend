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
        Schema::create('tukar_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_pengaju_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_penerima_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('shift_pengaju_id')->constrained('shifts')->onDelete('cascade');
            $table->foreignId('shift_penerima_id')->constrained('shifts')->onDelete('cascade');
            $table->text('alasan');
            $table->enum('status', ['pending_penerima', 'pending_admin', 'approved', 'rejected_penerima', 'rejected_admin'])->default('pending_penerima');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tukar_shifts');
    }
};
