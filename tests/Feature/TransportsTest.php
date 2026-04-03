<?php

namespace Tests\Feature;

use App\Models\Transport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_transport(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/transports', [
            'name' => 'Fast Logistics',
        ]);

        $response->assertRedirect('/transports');
        $this->assertDatabaseHas('transports', [
            'name' => 'Fast Logistics',
            'total_trips' => 0,
        ]);
    }

    public function test_user_can_update_transport(): void
    {
        $user = User::factory()->create();

        $transport = Transport::create([
            'display_id' => 'TRN-'.strtoupper(Str::random(6)),
            'company_scope_id' => 1,
            'name' => 'Old Name',
            'created_by_email' => $user->email,
            'total_trips' => 0,
        ]);

        $response = $this->actingAs($user)->put("/transports/{$transport->id}", [
            'name' => 'New Name',
            'vehicle' => 'Truck',
            'vehicle_number' => 'PB-65-AX-1234',
            'contact' => '9999999999',
            'total_trips' => 5,
        ]);

        $response->assertRedirect('/transports');
        $this->assertDatabaseHas('transports', [
            'id' => $transport->id,
            'name' => 'New Name',
            'vehicle' => 'Truck',
            'vehicle_number' => 'PB-65-AX-1234',
            'contact' => '9999999999',
            'total_trips' => 5,
        ]);
    }

    public function test_user_can_delete_transport(): void
    {
        $user = User::factory()->create();

        $transport = Transport::create([
            'display_id' => 'TRN-'.strtoupper(Str::random(6)),
            'company_scope_id' => 1,
            'name' => 'Delete Me',
            'created_by_email' => $user->email,
            'total_trips' => 0,
        ]);

        $response = $this->actingAs($user)->delete("/transports/{$transport->id}");

        $response->assertRedirect('/transports');
        $this->assertDatabaseMissing('transports', [
            'id' => $transport->id,
        ]);
    }
}

