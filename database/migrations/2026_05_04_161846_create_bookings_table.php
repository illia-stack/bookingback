<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('property_id')
                ->constrained()
                ->onDelete('cascade');

            $table->date('check_in');
            $table->date('check_out');

            $table->decimal('total_price', 10, 2);

            // ✅ FINAL STATUS (ENUM – wichtig für Stripe + Webhook + App Logic)
            $table->enum('status', [
                'pending',
                'paid',
                'cancelled',
                'failed'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};