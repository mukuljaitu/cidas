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
        Schema::create('employees_legacy', function (Blueprint $table) {
            $table->id();
            $table->string('Name'); // Capital N as in image
            $table->string('supervisor')->nullable();
            $table->string('state')->nullable();
            $table->text('cities')->nullable();
            $table->text('parties')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees_legacy');
    }
};
