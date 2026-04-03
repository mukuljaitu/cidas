<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'city')) {
                $table->string('city')->nullable();
            }

            if (! Schema::hasColumn('employees', 'state')) {
                $table->string('state')->nullable();
            }

            if (! Schema::hasColumn('employees', 'pin_code')) {
                $table->string('pin_code')->nullable();
            }
        });

        if (Schema::hasColumn('employees', 'location')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('location');
            });
        }

        if (Schema::hasColumn('employees', 'phone')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'location')) {
                $table->string('location')->nullable();
            }

            if (! Schema::hasColumn('employees', 'phone')) {
                $table->string('phone')->nullable();
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('employees', 'state')) {
                $table->dropColumn('state');
            }
            if (Schema::hasColumn('employees', 'pin_code')) {
                $table->dropColumn('pin_code');
            }
        });
    }
};
