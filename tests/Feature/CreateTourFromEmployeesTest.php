<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Employee;
use App\Models\Party;
use App\Models\Role;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateTourFromEmployeesTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_tour_page_shows_only_salesmen_by_state(): void
    {
        $user = User::factory()->create();

        $salesmanRole = Role::firstOrCreate(['name' => 'Salesman']);
        $accountRole = Role::firstOrCreate(['name' => 'Accountant']);

        $salesPunjab = Employee::create([
            'display_id' => 'EMP-'.strtoupper(Str::random(6)),
            'company_scope_id' => 1,
            'name' => 'Sales Punjab',
            'role_id' => $salesmanRole->id,
            'created_by_email' => $user->email,
            'status' => 'Active',
            'state' => 'Punjab',
        ]);
        $salesPunjab->roles()->sync([$salesmanRole->id]);

        $accountPunjab = Employee::create([
            'display_id' => 'EMP-'.strtoupper(Str::random(6)),
            'company_scope_id' => 1,
            'name' => 'Account Punjab',
            'role_id' => $accountRole->id,
            'created_by_email' => $user->email,
            'status' => 'Active',
            'state' => 'Punjab',
        ]);
        $accountPunjab->roles()->sync([$accountRole->id]);

        $salesRajasthan = Employee::create([
            'display_id' => 'EMP-'.strtoupper(Str::random(6)),
            'company_scope_id' => 1,
            'name' => 'Sales Rajasthan',
            'role_id' => $salesmanRole->id,
            'created_by_email' => $user->email,
            'status' => 'Active',
            'state' => 'Rajasthan',
        ]);
        $salesRajasthan->roles()->sync([$salesmanRole->id]);

        $response = $this->actingAs($user)->get('/create-tour?state=Punjab');

        $response->assertStatus(200);
        $response->assertSee('Sales Punjab');
        $response->assertDontSee('Account Punjab');
        $response->assertDontSee('Sales Rajasthan');
    }

    public function test_create_tour_uses_employee_parties_and_manual_cities_as_city_source(): void
    {
        $user = User::factory()->create();

        $salesmanRole = Role::firstOrCreate(['name' => 'Salesman']);
        $sales = Employee::create([
            'display_id' => 'EMP-'.strtoupper(Str::random(6)),
            'company_scope_id' => 1,
            'name' => 'Sales One',
            'role_id' => $salesmanRole->id,
            'created_by_email' => $user->email,
            'status' => 'Active',
            'state' => 'Punjab',
        ]);
        $sales->roles()->sync([$salesmanRole->id]);

        Party::create([
            'display_id' => 'PRT-'.strtoupper(Str::random(6)),
            'company_code' => null,
            'company_scope_id' => 1,
            'name' => 'Party A',
            'city' => 'Ludhiana',
            'employee_id' => $sales->id,
            'created_by_email' => $user->email,
            'status' => 'Active',
        ]);

        City::create([
            'employee_id' => $sales->id,
            'city' => 'Jaipur',
        ]);

        $response = $this->actingAs($user)->post('/create-tour?state=Punjab', [
            'employee_id' => (string) $sales->id,
            'tour_date' => now()->toDateString(),
            'cities' => ['Ludhiana'],
        ]);

        $response->assertRedirect('/create-tour?state=Punjab');
        $this->assertDatabaseHas('tours', [
            'employee_name' => 'Sales One',
            'state' => 'Punjab',
            'cities' => 'Ludhiana',
            'status' => '1',
        ]);

        $manualCity = $this->actingAs($user)->post('/create-tour?state=Punjab', [
            'employee_id' => (string) $sales->id,
            'tour_date' => now()->toDateString(),
            'cities' => ['Jaipur'],
        ]);

        $manualCity->assertRedirect('/create-tour?state=Punjab');
        $this->assertDatabaseHas('tours', [
            'employee_name' => 'Sales One',
            'state' => 'Punjab',
            'cities' => 'Jaipur',
            'status' => '1',
        ]);

        $bad = $this->actingAs($user)->post('/create-tour?state=Punjab', [
            'employee_id' => (string) $sales->id,
            'tour_date' => now()->toDateString(),
            'cities' => ['Udaipur'],
        ]);

        $bad->assertRedirect();
        $this->assertSame(2, Tour::query()->count());
    }
}
