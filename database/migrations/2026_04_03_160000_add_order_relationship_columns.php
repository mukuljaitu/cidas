<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'salesman_id')) {
                $table->foreignId('salesman_id')->nullable()->after('salesman')->constrained('employees')->nullOnDelete();
            }

            if (! Schema::hasColumn('orders', 'party_id')) {
                $table->foreignId('party_id')->nullable()->after('party')->constrained('parties')->nullOnDelete();
            }

            if (! Schema::hasColumn('orders', 'transport_id')) {
                $table->foreignId('transport_id')->nullable()->after('transport')->constrained('transports')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'transport_id')) {
                $table->dropConstrainedForeignId('transport_id');
            }
            if (Schema::hasColumn('orders', 'party_id')) {
                $table->dropConstrainedForeignId('party_id');
            }
            if (Schema::hasColumn('orders', 'salesman_id')) {
                $table->dropConstrainedForeignId('salesman_id');
            }
        });
    }
};

