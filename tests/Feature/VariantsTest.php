<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class VariantsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_variant_from_products_page(): void
    {
        $user = User::factory()->create();

        $product = Product::create([
            'company_id' => 1,
            'display_id' => 'PRD-'.strtoupper(Str::random(6)),
            'name' => 'Product A',
            'description' => null,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->post('/variants', [
            'redirect_to' => 'products',
            'product_id' => (string) $product->id,
            'name' => 'Variant 1',
            'sku' => 'SKU1',
            'unit' => 'pcs',
            'size' => '1',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('variants', [
            'product_id' => $product->id,
            'name' => 'Variant 1',
            'sku' => 'SKU1',
            'unit' => 'pcs',
            'size' => '1',
        ]);
    }

    public function test_user_can_update_variant(): void
    {
        $user = User::factory()->create();

        $product = Product::create([
            'company_id' => 1,
            'display_id' => 'PRD-'.strtoupper(Str::random(6)),
            'name' => 'Product A',
            'description' => null,
            'created_by' => $user->id,
        ]);

        $variant = Variant::create([
            'company_id' => 1,
            'product_id' => $product->id,
            'display_id' => 'VAR-'.strtoupper(Str::random(6)),
            'name' => 'Old Variant',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->put("/variants/{$variant->id}", [
            'product_id' => (string) $product->id,
            'name' => 'New Variant',
            'sku' => 'SKU2',
            'unit' => 'ltr',
            'size' => '500ml',
        ]);

        $response->assertRedirect('/variants');
        $this->assertDatabaseHas('variants', [
            'id' => $variant->id,
            'name' => 'New Variant',
            'sku' => 'SKU2',
            'unit' => 'ltr',
            'size' => '500ml',
        ]);
    }
}
