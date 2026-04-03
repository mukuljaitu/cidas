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
        Schema::create('employees', function (Blueprint $table) {
            $table->id(); // Internal ID (Primary Key) [cite: 20]

            // Custom IDs
            $table->string('display_id')->unique(); // e.g., CMP1-EMP-001
            $table->unsignedBigInteger('company_scope_id');

            // Basic Info [cite: 34]
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();

            // Role Reference [cite: 3, 36]
            $table->foreignId('role_id')->constrained();

            // Tracking [cite: 13, 14, 16, 17]
            $table->string('created_by_email');
            $table->softDeletes(); // For "Is delete" functionality
            $table->timestamps(); // Standard Create/Update dates [cite: 13, 14]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
