<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('employees', 'email')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropUnique('employees_email_unique');
            });
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'email',
                'date',
                'cities',
                'is_supervisor',
                'deleted_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('name');
            $table->date('date')->nullable()->after('name');
            $table->json('cities')->nullable()->after('date');
            $table->boolean('is_supervisor')->default(false)->after('cities');
            $table->softDeletes();
        });
    }
};
