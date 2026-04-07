<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_product(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/products', [
            'name' => 'Product A',
            'type' => 'Fer',
            'description' => 'Demo',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'name' => 'Product A',
            'description' => 'Demo',
        ]);
    }

    public function test_user_can_update_product(): void
    {
        $user = User::factory()->create();

        $product = Product::create([
            'company_id' => 1,
            'display_id' => 'PRD-'.strtoupper(Str::random(6)),
            'name' => 'Old',
            'type' => 'Fer',
            'description' => null,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->put("/products/{$product->id}", [
            'name' => 'New',
            'type' => 'Pes',
            'description' => 'Updated',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New',
            'description' => 'Updated',
        ]);
    }
}
