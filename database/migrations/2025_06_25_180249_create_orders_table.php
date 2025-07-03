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
            $table->string('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('housenumber')->nullable();
            $table->string('addition')->nullable();
            $table->enum('type', ['afhalen', 'bezorgen']);
            $table->time('pickup_time')->nullable();
            $table->decimal('total_price', 8, 2);
            $table->text('note')->nullable();
            $table->boolean('paid')->default(false);
            $table->string('payment_id')->nullable()->after('id');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
