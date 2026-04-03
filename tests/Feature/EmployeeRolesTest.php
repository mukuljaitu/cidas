<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_have_multiple_roles_and_keeps_primary_role(): void
    {
        $user = User::factory()->create();

        $primary = Role::firstOrCreate(['name' => 'Salesman']);
        $secondary = Role::firstOrCreate(['name' => 'Accountant']);

        $response = $this->actingAs($user)->post('/employees', [
            'name' => 'Multi Role',
            'role_id' => (string) $primary->id,
            'role_ids' => [(string) $secondary->id],
        ]);

        $response->assertRedirect('/employees');

        $employee = Employee::query()->where('name', 'Multi Role')->firstOrFail();
        $this->assertSame($primary->id, $employee->role_id);
        $this->assertDatabaseHas('employee_role', ['employee_id' => $employee->id, 'role_id' => $primary->id]);
        $this->assertDatabaseHas('employee_role', ['employee_id' => $employee->id, 'role_id' => $secondary->id]);
    }
}

