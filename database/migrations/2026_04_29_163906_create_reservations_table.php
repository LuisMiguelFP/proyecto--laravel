<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum('status', [
                'pendiente',
                'confirmada',
                'rechazada',
                'cancelada',
                'finalizada',
            ])->default('pendiente');
            $table->string('user_name');
            $table->string('user_email');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};