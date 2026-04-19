<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('city');
            $table->timestamps();

            $table->unique(['employee_id', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
