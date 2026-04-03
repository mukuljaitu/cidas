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
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('state');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('party_id')->constrained()->onDelete('cascade');
            $table->string('station')->nullable();
            $table->string('issuing_bank')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('reference_no')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('receiving_bank')->nullable();
            $table->date('clear_date')->nullable();
            $table->text('comments')->nullable();
            $table->json('image_paths')->nullable();
            $table->enum('status', ['Pending', 'Cleared', 'Return'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
