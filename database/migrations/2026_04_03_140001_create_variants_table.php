<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('variants')) {
            return;
        }

        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->default(1);
            $table->unsignedBigInteger('product_id');
            $table->string('display_id')->unique();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('unit')->nullable();
            $table->string('size')->nullable();
            $table->string('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('variants')) {
            return;
        }

        Schema::dropIfExists('variants');
    }
};

