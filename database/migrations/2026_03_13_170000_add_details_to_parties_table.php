<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->string('district')->nullable()->after('city');
            $table->string('alias')->nullable()->after('name');
            $table->string('gst_no', 32)->nullable()->after('mobile');
            $table->text('street_address')->nullable()->after('gst_no');

            $table->string('bank_name')->nullable()->after('street_address');
            $table->string('bank_account_no')->nullable()->after('bank_name');
            $table->string('bank_ifsc', 32)->nullable()->after('bank_account_no');

            $table->string('party_type')->nullable()->after('status');
            $table->string('pan_no', 32)->nullable()->after('party_type');
            $table->string('aadhar_card', 32)->nullable()->after('pan_no');
            $table->string('owner_name')->nullable()->after('aadhar_card');

            $table->string('pest_lic')->nullable()->after('owner_name');
            $table->string('fert_lic')->nullable()->after('pest_lic');
            $table->string('seed_lic')->nullable()->after('fert_lic');

            $table->string('cq1')->nullable()->after('seed_lic');
            $table->string('cq2')->nullable()->after('cq1');

            $table->string('stamp')->nullable()->after('cq2');
            $table->string('sign')->nullable()->after('stamp');
        });
    }

    public function down(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->dropColumn([
                'district',
                'alias',
                'gst_no',
                'street_address',
                'bank_name',
                'bank_account_no',
                'bank_ifsc',
                'party_type',
                'pan_no',
                'aadhar_card',
                'owner_name',
                'pest_lic',
                'fert_lic',
                'seed_lic',
                'cq1',
                'cq2',
                'stamp',
                'sign',
            ]);
        });
    }
};
