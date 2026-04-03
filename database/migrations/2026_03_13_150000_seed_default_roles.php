<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roles = ['MD', 'Salesman', 'Accountant', 'Stock supervisor'];
        foreach ($roles as $name) {
            $exists = DB::table('roles')->where('name', $name)->exists();
            if (! $exists) {
                DB::table('roles')->insert([
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('roles')->whereIn('name', ['MD', 'Salesman', 'Accountant', 'Stock supervisor'])->delete();
    }
};
