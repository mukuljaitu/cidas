<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->string('name')->toString();

        $query = Product::query()
            ->withCount('variants')
            ->orderBy('id', 'desc');

        if ($name !== '' && $name !== 'All') {
            $query->where('name', $name);
        }

        $products = $query->paginate($request->get('pageSize', 50))->withQueryString();

        $names = Product::query()
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        $productOptions = Product::query()->orderBy('name')->get(['id', 'name']);

        return view('products.index', compact('products', 'names', 'productOptions'));
    }

    public function variants(Product $product)
    {
        $variants = $product->variants()
            ->get(['id', 'display_id', 'name', 'sku', 'unit', 'size', 'created_at'])
            ->sort(function ($a, $b) {
                $extractLeadingNumber = static function ($value): ?float {
                    $value = trim((string) ($value ?? ''));
                    if ($value === '') {
                        return null;
                    }
                    if (preg_match('/^\s*([0-9]+(?:\.[0-9]+)?)/', $value, $m) !== 1) {
                        return null;
                    }

                    return (float) $m[1];
                };

                $aNum = $extractLeadingNumber($a->size);
                $bNum = $extractLeadingNumber($b->size);
                $aHasNum = $aNum !== null;
                $bHasNum = $bNum !== null;

                if ($aHasNum !== $bHasNum) {
                    return $aHasNum ? -1 : 1;
                }

                if ($aHasNum && $bHasNum) {
                    $cmp = $aNum <=> $bNum;
                    if ($cmp !== 0) {
                        return $cmp;
                    }
                }

                $aUnit = strtolower(trim((string) ($a->unit ?? '')));
                $bUnit = strtolower(trim((string) ($b->unit ?? '')));
                if ($aUnit !== $bUnit) {
                    return $aUnit <=> $bUnit;
                }

                $aName = strtolower(trim((string) ($a->name ?? '')));
                $bName = strtolower(trim((string) ($b->name ?? '')));
                if ($aName !== $bName) {
                    return $aName <=> $bName;
                }

                return ((int) $a->id) <=> ((int) $b->id);
            })
            ->values();

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'display_id' => $product->display_id,
            ],
            'variants' => $variants,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:Fer,Pes'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        do {
            $displayId = 'PRD-'.strtoupper(Str::random(6));
        } while (Product::query()->where('display_id', $displayId)->exists());

        $createdBy = optional($request->user())->id ?? 0;

        Product::create([
            'company_id' => 1,
            'display_id' => $displayId,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'created_by' => $createdBy,
        ]);

        return redirect('/products')->with('status', 'product-created');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:Fer,Pes'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $product->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect('/products')->with('status', 'product-updated');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect('/products')->with('status', 'product-deleted');
    }
}
