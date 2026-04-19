<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CitiesTest extends TestCase
{
    use RefreshDatabase;

    private function createEmployee(User $user, string $name = 'Sales One'): Employee
    {
        $role = Role::firstOrCreate(['name' => 'Salesman']);

        return Employee::create([
            'display_id' => 'EMP-' . strtoupper(Str::random(6)),
            'company_scope_id' => 1,
            'name' => $name,
            'role_id' => $role->id,
            'created_by_email' => $user->email,
            'status' => 'Active',
        ]);
    }

    public function test_user_can_create_city(): void
    {
        $user = User::factory()->create();
        $employee = $this->createEmployee($user);

        $response = $this->actingAs($user)->post('/cities', [
            'city' => 'Patiala',
            'employee_id' => $employee->id,
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', [
            'city' => 'Patiala',
            'employee_id' => $employee->id,
        ]);
    }

    public function test_user_can_update_city(): void
    {
        $user = User::factory()->create();
        $employeeA = $this->createEmployee($user, 'Sales A');
        $employeeB = $this->createEmployee($user, 'Sales B');

        $city = City::create([
            'city' => 'Bathinda',
            'employee_id' => $employeeA->id,
        ]);

        $response = $this->actingAs($user)->put("/cities/{$city->id}", [
            'city' => 'Bikaner',
            'employee_id' => $employeeB->id,
        ]);

        $response->assertRedirect('/cities');
        $this->assertDatabaseHas('cities', [
            'id' => $city->id,
            'city' => 'Bikaner',
            'employee_id' => $employeeB->id,
        ]);
    }

    public function test_user_can_delete_city(): void
    {
        $user = User::factory()->create();
        $employee = $this->createEmployee($user);

        $city = City::create([
            'city' => 'Moga',
            'employee_id' => $employee->id,
        ]);

        $response = $this->actingAs($user)->delete("/cities/{$city->id}");

        $response->assertRedirect('/cities');
        $this->assertDatabaseMissing('cities', [
            'id' => $city->id,
        ]);
    }
}
