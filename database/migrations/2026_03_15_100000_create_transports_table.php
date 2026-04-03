<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->string('display_id')->unique();
            $table->unsignedBigInteger('company_scope_id');

            $table->string('name');
            $table->string('vehicle')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('contact')->nullable();
            $table->date('last_trip')->nullable();
            $table->unsignedInteger('total_trips')->default(0);
            $table->date('date_of_joining')->nullable();

            $table->string('created_by_email');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transports');
    }
};
