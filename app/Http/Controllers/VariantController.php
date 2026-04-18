<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'size' => ['nullable', 'string', 'max:255'],
        ]);

        $createdBy = optional($request->user())->id ?? 0;

        $productId = (int) $validated['product_id'];
        $sku = $this->normalizeOptionalString($validated['sku'] ?? null);
        $unit = $this->normalizeOptionalString($validated['unit'] ?? null);
        $sizeInput = $this->normalizeOptionalString($validated['size'] ?? null);

        $sizes = $sizeInput !== null ? $this->splitMultiValue($sizeInput) : [];
        if (count($sizes) <= 1) {
            $size = $sizeInput;
            Variant::create([
                'company_id' => 1,
                'product_id' => $productId,
                'display_id' => $this->generateDisplayId(),
                'name' => $validated['name'],
                'sku' => $sku,
                'unit' => $unit,
                'size' => $size,
                'created_by' => $createdBy,
            ]);
        } else {
            DB::transaction(function () use ($sizes, $productId, $sku, $unit, $createdBy) {
                foreach ($sizes as $size) {
                    Variant::create([
                        'company_id' => 1,
                        'product_id' => $productId,
                        'display_id' => $this->generateDisplayId(),
                        'name' => $this->computeVariantName($size, $unit, $sku),
                        'sku' => $sku,
                        'unit' => $unit,
                        'size' => $size,
                        'created_by' => $createdBy,
                    ]);
                }
            });
        }

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
            'size' => ['nullable', 'string', 'max:255'],
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

    private function generateDisplayId(): string
    {
        do {
            $displayId = 'VAR-'.strtoupper(Str::random(6));
        } while (Variant::query()->where('display_id', $displayId)->exists());

        return $displayId;
    }

    private function normalizeOptionalString(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : '';
        return $value === '' ? null : $value;
    }

    private function splitMultiValue(string $value): array
    {
        $parts = preg_split('/[,\n;|]+/', $value) ?: [];

        $unique = [];
        foreach ($parts as $part) {
            $part = trim((string) $part);
            if ($part === '') {
                continue;
            }
            $unique[$part] = true;
        }

        return array_values(array_keys($unique));
    }

    private function computeVariantName(string $size, ?string $unit, ?string $sku): string
    {
        $sizeUnit = trim($size.($unit ?? ''));
        $parts = array_filter([$sizeUnit !== '' ? $sizeUnit : null, $sku]);

        return trim(implode(' ', $parts));
    }
}
