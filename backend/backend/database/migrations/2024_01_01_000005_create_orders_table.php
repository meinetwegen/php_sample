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
            $table->foreignId('user_id')->constrained();
            $table->string('status', 20)->default('pending');
            $table->string('payment_method', 30);
            $table->string('payment_status', 20)->default('pending');
            $table->unsignedInteger('subtotal_cents');
            $table->unsignedInteger('shipping_cents');
            $table->unsignedInteger('total_cents');
            $table->string('shipping_name', 120);
            $table->string('shipping_address', 250);
            $table->string('shipping_city', 100);
            $table->string('shipping_zip', 20);
            $table->string('shipping_country', 60);
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('magazine_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->unsignedInteger('price_cents');
            $table->unsignedSmallInteger('quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
