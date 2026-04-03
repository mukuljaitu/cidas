<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VariantController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->string('name')->toString();
        $productId = $request->string('product_id')->toString();

        $query = Variant::query()->with('product')->orderBy('id', 'desc');

        if ($name !== '' && $name !== 'All') {
            $query->where('name', $name);
        }

        if ($productId !== '' && $productId !== 'All') {
            $query->where('product_id', $productId);
        }

        $variants = $query->paginate($request->get('pageSize', 50))->withQueryString();

        $names = Variant::query()->select('name')->distinct()->orderBy('name')->pluck('name');
        $productOptions = Product::query()->orderBy('name')->get(['id', 'name']);

        return view('variants.index', compact('variants', 'names', 'productOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:50'],
            'size' => ['nullable', 'string', 'max:50'],
        ]);

        do {
            $displayId = 'VAR-'.strtoupper(Str::random(6));
        } while (Variant::query()->where('display_id', $displayId)->exists());

        $createdBy = optional($request->user())->id ?? 0;

        Variant::create([
            'company_id' => 1,
            'product_id' => (int) $validated['product_id'],
            'display_id' => $displayId,
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?? null,
            'unit' => $validated['unit'] ?? null,
            'size' => $validated['size'] ?? null,
            'created_by' => $createdBy,
        ]);

        $redirectTo = $request->string('redirect_to')->toString();
        if ($redirectTo === 'products') {
            return redirect('/products')->with('status', 'variant-created');
        }

        return redirect('/variants')->with('status', 'variant-created');
    }

    public function update(Request $request, Variant $variant)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:50'],
            'size' => ['nullable', 'string', 'max:50'],
        ]);

        $variant->update([
            'product_id' => (int) $validated['product_id'],
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?? null,
            'unit' => $validated['unit'] ?? null,
            'size' => $validated['size'] ?? null,
        ]);

        return redirect('/variants')->with('status', 'variant-updated');
    }

    public function destroy(Variant $variant)
    {
        $variant->delete();

        return redirect('/variants')->with('status', 'variant-deleted');
    }
}
