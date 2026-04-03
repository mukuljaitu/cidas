<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('mobile')->nullable()->after('email');
            $table->string('location')->nullable()->after('mobile');
            $table->date('date_of_joining')->nullable()->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['mobile', 'location', 'date_of_joining']);
        });
    }
};
