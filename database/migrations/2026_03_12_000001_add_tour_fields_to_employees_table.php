<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->date('date')->nullable()->after('name');
            $table->json('cities')->nullable()->after('date');
            $table->string('status')->default('Tour')->after('cities');
            $table->boolean('is_supervisor')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['date', 'cities', 'status', 'is_supervisor']);
        });
    }
};
