<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Party;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PartiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_party_and_assign_salesman(): void
    {
        $user = User::factory()->create();

        $salesmanRole = Role::firstOrCreate(['name' => 'Salesman']);
        $employee = Employee::create([
            'display_id' => 'EMP-'.strtoupper(Str::random(6)),
            'company_scope_id' => 1,
            'name' => 'John Sales',
            'role_id' => $salesmanRole->id,
            'created_by_email' => $user->email,
            'status' => 'Active',
        ]);
        $employee->roles()->sync([$salesmanRole->id]);

        $response = $this->actingAs($user)->post('/parties', [
            'name' => 'ABC Traders',
            'employee_id' => (string) $employee->id,
        ]);

        $response->assertRedirect('/parties');
        $this->assertDatabaseHas('parties', [
            'name' => 'ABC Traders',
            'employee_id' => $employee->id,
        ]);
    }

    public function test_party_employee_must_be_salesman_when_salesman_role_exists(): void
    {
        $user = User::factory()->create();

        $salesmanRole = Role::firstOrCreate(['name' => 'Salesman']);
        $managerRole = Role::create(['name' => 'Manager']);

        $manager = Employee::create([
            'display_id' => 'EMP-'.strtoupper(Str::random(6)),
            'company_scope_id' => 1,
            'name' => 'Mary Manager',
            'role_id' => $managerRole->id,
            'created_by_email' => $user->email,
            'status' => 'Active',
        ]);
        $manager->roles()->sync([$managerRole->id]);

        $response = $this->actingAs($user)->post('/parties', [
            'name' => 'XYZ Retail',
            'employee_id' => (string) $manager->id,
        ]);

        $response->assertSessionHasErrors(['employee_id']);
        $this->assertDatabaseMissing('parties', [
            'name' => 'XYZ Retail',
        ]);
    }

    public function test_user_can_update_party_salesman(): void
    {
        $user = User::factory()->create();

        $salesmanRole = Role::firstOrCreate(['name' => 'Salesman']);
        $sales1 = Employee::create([
            'display_id' => 'EMP-'.strtoupper(Str::random(6)),
            'company_scope_id' => 1,
            'name' => 'Sales One',
            'role_id' => $salesmanRole->id,
            'created_by_email' => $user->email,
            'status' => 'Active',
        ]);
        $sales1->roles()->sync([$salesmanRole->id]);

        $sales2 = Employee::create([
            'display_id' => 'EMP-'.strtoupper(Str::random(6)),
            'company_scope_id' => 1,
            'name' => 'Sales Two',
            'role_id' => $salesmanRole->id,
            'created_by_email' => $user->email,
            'status' => 'Active',
        ]);
        $sales2->roles()->sync([$salesmanRole->id]);

        $party = Party::create([
            'display_id' => 'PRT-'.strtoupper(Str::random(6)),
            'company_code' => null,
            'company_scope_id' => 1,
            'name' => 'Update Me',
            'employee_id' => $sales1->id,
            'created_by_email' => $user->email,
            'status' => 'Active',
        ]);

        $response = $this->actingAs($user)->put("/parties/{$party->id}", [
            'name' => 'Update Me',
            'employee_id' => (string) $sales2->id,
            'status' => 'Active',
        ]);

        $response->assertRedirect('/parties');
        $this->assertDatabaseHas('parties', [
            'id' => $party->id,
            'employee_id' => $sales2->id,
        ]);
    }

    public function test_parties_can_be_filtered_by_verification_status(): void
    {
        $user = User::factory()->create();

        $salesmanRole = Role::firstOrCreate(['name' => 'Salesman']);
        $employee = Employee::create([
            'display_id' => 'EMP-'.strtoupper(Str::random(6)),
            'company_scope_id' => 1,
            'name' => 'Filter Sales',
            'role_id' => $salesmanRole->id,
            'created_by_email' => $user->email,
            'status' => 'Active',
        ]);
        $employee->roles()->sync([$salesmanRole->id]);

        $verifiedParty = Party::create([
            'display_id' => 'PRT-'.strtoupper(Str::random(6)),
            'company_scope_id' => 1,
            'name' => 'Verified Party',
            'employee_id' => $employee->id,
            'created_by_email' => $user->email,
            'status' => 'Active',
            'is_verified' => true,
        ]);
        $pendingParty = Party::create([
            'display_id' => 'PRT-'.strtoupper(Str::random(6)),
            'company_scope_id' => 1,
            'name' => 'Pending Party',
            'employee_id' => $employee->id,
            'created_by_email' => $user->email,
            'status' => 'Active',
            'is_verified' => false,
        ]);

        $resVerified = $this->actingAs($user)->get('/parties?verified=Verified');
        $resVerified->assertOk();
        $resVerified->assertSee('row-'.$verifiedParty->id);
        $resVerified->assertDontSee('row-'.$pendingParty->id);

        $resPending = $this->actingAs($user)->get('/parties?verified=Pending');
        $resPending->assertOk();
        $resPending->assertSee('row-'.$pendingParty->id);
        $resPending->assertDontSee('row-'.$verifiedParty->id);
    }
}
