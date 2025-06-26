<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');

            // Alleen verplicht bij bezorgen
            $table->string('address')->nullable();
            $table->string('postcode')->nullable();

            // 'afhalen' of 'bezorgen'
            $table->enum('type', ['afhalen', 'bezorgen']);

            // Alleen verplicht bij afhalen
            $table->time('pickup_time')->nullable();

            $table->decimal('total_price', 8, 2);

            // Optioneel
            $table->text('note')->nullable();          // extra opmerkingen
            $table->boolean('paid')->default(false);   // handig voor betaling later

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
