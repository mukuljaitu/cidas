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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->date('order_date');
            $table->string('salesman');
            $table->string('party');
            $table->string('bill_type')->default('A');
            $table->date('bill_date')->nullable();
            $table->string('bill_no')->nullable();
            $table->string('transport')->nullable();
            $table->string('status')->default('Incomplete');
            $table->string('type')->nullable(); // Fer or Pes
            $table->json('receiving_image_path')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('product');
            $table->string('packing')->nullable();
            $table->string('size')->nullable();
            $table->integer('quantity');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
