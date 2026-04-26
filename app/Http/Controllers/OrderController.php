<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Party;
use App\Models\Product;
use App\Models\Transport;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    private function billFiscalYearLabel(Carbon $date): string
    {
        $y = (int) $date->format('y');
        $m = (int) $date->format('n');

        if ($m >= 4) {
            $start = $y;
            $end = ($y + 1) % 100;
        } else {
            $start = ($y + 99) % 100;
            $end = $y;
        }

        return sprintf('%02d-%02d', $start, $end);
    }

    private function nextBillNoForOrder(Order $order, Carbon $billDate): string
    {
        $letter = $order->type === 'Pes' ? 'P' : 'F';
        $fy = $this->billFiscalYearLabel($billDate);

        $patternPrefix = 'CCC/' . $letter . '/';
        $patternSuffix = '/' . $fy;

        $existing = Order::query()
            ->where('is_deleted', false)
            ->where('bill_type', 'A')
            ->where('type', $order->type)
            ->whereNotNull('bill_no')
            ->where('bill_no', 'like', $patternPrefix . '%' . $patternSuffix)
            ->lockForUpdate()
            ->pluck('bill_no');

        $maxSeq = 0;
        foreach ($existing as $billNo) {
            $billNo = (string) $billNo;
            if (! str_starts_with($billNo, $patternPrefix) || ! str_ends_with($billNo, $patternSuffix)) {
                continue;
            }

            $middle = substr($billNo, strlen($patternPrefix), -strlen($patternSuffix));
            $seq = (int) preg_replace('/[^0-9]/', '', (string) $middle);
            if ($seq > $maxSeq) {
                $maxSeq = $seq;
            }
        }

        $next = $maxSeq + 1;

        return 'CCC/' . $letter . '/' . $next . '/' . $fy;
    }

    public function index(Request $request)
    {
        return view('orders.index');
    }

    public function analyze(Request $request)
    {
        $maxDays = 365;
        $defaultDays = 90;

        $requestedDays = (int) $request->query('days', $defaultDays);
        if ($requestedDays <= 0) {
            $requestedDays = $defaultDays;
        }
        if ($requestedDays > $maxDays) {
            $requestedDays = $maxDays;
        }

        $startParam = trim((string) $request->query('start', ''));
        $endParam = trim((string) $request->query('end', ''));

        $usingCustom = false;
        $end = Carbon::today();
        $start = Carbon::today()->subDays($requestedDays - 1);

        if ($startParam !== '' && $endParam !== '') {
            try {
                $customStart = Carbon::parse($startParam)->startOfDay();
                $customEnd = Carbon::parse($endParam)->startOfDay();

                if ($customEnd->gt(Carbon::today())) {
                    $customEnd = Carbon::today();
                }

                if ($customStart->lte($customEnd)) {
                    $usingCustom = true;
                    $end = $customEnd;
                    $days = $customStart->diffInDays($customEnd) + 1;
                    if ($days > $maxDays) {
                        $days = $maxDays;
                        $start = $end->copy()->subDays($days - 1);
                    } else {
                        $start = $customStart;
                    }
                }
            } catch (\Throwable $e) {
            }
        }

        if (! $usingCustom) {
            $days = $requestedDays;
        }

        $labels = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $labels[] = $cursor->toDateString();
            $cursor->addDay();
        }

        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.is_deleted', false)
            ->where('order_items.is_deleted', false)
            ->whereBetween('orders.order_date', [$start->toDateString(), $end->toDateString()])
            ->select('order_items.product', DB::raw('SUM(order_items.quantity) as qty'))
            ->groupBy('order_items.product')
            ->orderByDesc('qty')
            ->limit(6)
            ->pluck('order_items.product')
            ->map(fn($p) => trim((string) $p))
            ->filter(fn($p) => $p !== '')
            ->values();

        $productRows = $topProducts->isEmpty()
            ? collect()
            : DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.is_deleted', false)
            ->where('order_items.is_deleted', false)
            ->whereBetween('orders.order_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('order_items.product', $topProducts->all())
            ->select('orders.order_date as day', 'order_items.product', DB::raw('SUM(order_items.quantity) as qty'))
            ->groupBy('orders.order_date', 'order_items.product')
            ->orderBy('orders.order_date')
            ->get();

        $productByKey = [];
        foreach ($productRows as $r) {
            $day = (string) $r->day;
            $product = trim((string) $r->product);
            $qty = (int) ($r->qty ?? 0);
            $productByKey[$product][$day] = $qty;
        }

        $productDatasets = [];
        foreach ($topProducts as $product) {
            $points = [];
            foreach ($labels as $day) {
                $points[] = (int) ($productByKey[$product][$day] ?? 0);
            }
            $productDatasets[] = [
                'label' => $product,
                'data' => $points,
            ];
        }

        $topCities = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('parties', 'parties.id', '=', 'orders.party_id')
            ->where('orders.is_deleted', false)
            ->where('order_items.is_deleted', false)
            ->whereBetween('orders.order_date', [$start->toDateString(), $end->toDateString()])
            ->whereNotNull('parties.city')
            ->whereRaw("TRIM(parties.city) <> ''")
            ->select(DB::raw('TRIM(parties.city) as city'), DB::raw('SUM(order_items.quantity) as qty'))
            ->groupBy(DB::raw('TRIM(parties.city)'))
            ->orderByDesc('qty')
            ->limit(6)
            ->pluck('city')
            ->map(fn($c) => trim((string) $c))
            ->filter(fn($c) => $c !== '')
            ->values();

        $cityRows = $topCities->isEmpty()
            ? collect()
            : DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('parties', 'parties.id', '=', 'orders.party_id')
            ->where('orders.is_deleted', false)
            ->where('order_items.is_deleted', false)
            ->whereBetween('orders.order_date', [$start->toDateString(), $end->toDateString()])
            ->whereNotNull('parties.city')
            ->whereRaw("TRIM(parties.city) <> ''")
            ->whereIn(DB::raw('TRIM(parties.city)'), $topCities->all())
            ->select('orders.order_date as day', DB::raw('TRIM(parties.city) as city'), DB::raw('SUM(order_items.quantity) as qty'))
            ->groupBy('orders.order_date', DB::raw('TRIM(parties.city)'))
            ->orderBy('orders.order_date')
            ->get();

        $cityByKey = [];
        foreach ($cityRows as $r) {
            $day = (string) $r->day;
            $city = trim((string) $r->city);
            $qty = (int) ($r->qty ?? 0);
            if ($city === '') {
                continue;
            }
            $cityByKey[$city][$day] = $qty;
        }

        $cityDatasets = [];
        foreach ($topCities as $city) {
            $points = [];
            foreach ($labels as $day) {
                $points[] = (int) ($cityByKey[$city][$day] ?? 0);
            }
            $cityDatasets[] = [
                'label' => $city,
                'data' => $points,
            ];
        }

        return view('orders.analyze', [
            'range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
                'days' => $days,
            ],
            'rangeUi' => [
                'mode' => $usingCustom ? 'custom' : 'days',
                'days' => $days,
                'quickDays' => [7, 30, 90, 180, 365],
            ],
            'productChart' => [
                'labels' => $labels,
                'datasets' => $productDatasets,
            ],
            'cityChart' => [
                'labels' => $labels,
                'datasets' => $cityDatasets,
            ],
        ]);
    }

    public function apiList(Request $request)
    {
        $orders = Order::query()
            ->withCount(['items' => function ($q) {
                $q->where('is_deleted', false);
            }])
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->get([
                'id',
                'order_date',
                'salesman',
                'salesman_id',
                'party',
                'party_id',
                'bill_type',
                'bill_date',
                'bill_no',
                'transport',
                'transport_id',
                'status',
                'type',
                'receiving_image_path',
                'is_deleted',
                'created_at',
            ]);

        return response()->json($orders);
    }

    public function apiDetails(Order $order)
    {
        if ((bool) $order->is_deleted) {
            abort(404);
        }

        $order->load([
            'items' => function ($q) {
                $q->where('is_deleted', false);
            },
            'transportRef',
        ]);

        return response()->json([
            'order' => $order,
            'transport' => $order->transportRef,
        ]);
    }

    public function apiItemsBulk(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'max:500'],
            'ids.*' => ['required', 'integer'],
        ]);

        $rows = OrderItem::query()
            ->whereIn('order_id', $validated['ids'])
            ->where('is_deleted', false)
            ->orderBy('id')
            ->get();

        $byOrder = [];
        foreach ($rows as $row) {
            $key = (string) $row->order_id;
            if (! array_key_exists($key, $byOrder)) {
                $byOrder[$key] = [];
            }
            $byOrder[$key][] = $row;
        }

        return response()->json($byOrder);
    }

    public function apiSalesmen()
    {
        $salesmen = Employee::query()
            ->whereHas('roles', function ($q) {
                $q->whereRaw('LOWER(name) like ?', ['%salesman%']);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($salesmen);
    }

    public function apiParties(Request $request)
    {
        $salesmanId = $request->integer('salesman_id');

        $query = Party::query()->orderBy('name');
        if ($salesmanId > 0) {
            $query->where('employee_id', $salesmanId);
        }

        return response()->json($query->get(['id', 'name', 'employee_id']));
    }

    public function apiTransports()
    {
        return response()->json(
            Transport::query()
                ->orderBy('name')
                ->get(['id', 'name', 'vehicle', 'vehicle_number', 'contact'])
        );
    }

    public function apiTransportDetails(Transport $transport)
    {
        return response()->json($transport->only(['id', 'name', 'vehicle', 'vehicle_number', 'contact']));
    }

    public function apiProducts(Request $request)
    {
        $type = $request->string('type')->toString();

        $query = Product::query()->orderBy('name');
        if ($type === 'Fer' || $type === 'Pes') {
            $query->where('type', $type);
        }

        return response()->json($query->get(['id', 'name', 'type']));
    }

    public function apiProductPackings(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $variants = Variant::query()
            ->where('product_id', $validated['product_id'])
            ->orderBy('id')
            ->get(['id', 'sku', 'size', 'unit', 'name']);

        $packings = [];
        $sizes = [];
        foreach ($variants as $v) {
            $sku = trim((string) ($v->sku ?? ''));
            if ($sku !== '') {
                $packings[$sku] = true;
            }

            $size = trim((string) ($v->size ?? ''));
            $unit = trim((string) ($v->unit ?? ''));
            $label = trim($size . $unit);
            if ($label !== '') {
                $sizes[$label] = true;
            } elseif (trim((string) $v->name) !== '') {
                $sizes[trim((string) $v->name)] = true;
            }
        }

        if (count($packings) === 0) {
            $packings['Case'] = true;
        }

        return response()->json([
            'packings' => array_values(array_keys($packings)),
            'sizes' => array_values(array_keys($sizes)),
            'variants' => $variants,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_date' => 'required|date',
            'salesman_id' => 'required|integer|exists:employees,id',
            'party_id' => 'required|integer|exists:parties,id',
            'bill_type' => 'required|string|in:A,B',
            'bill_no' => 'nullable|string',
            'status' => 'required|string|in:Incomplete,Finalized,Received,Okay,Cancelled',
            'type' => 'required|string|in:Fer,Pes',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.packing' => 'nullable|string',
            'items.*.size' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $salesmanId = (int) $validated['salesman_id'];
        $partyId = (int) $validated['party_id'];

        $isSalesmanEligible = Employee::query()
            ->whereKey($salesmanId)
            ->whereHas('roles', function ($q) {
                $q->whereRaw('LOWER(name) like ?', ['%salesman%']);
            })
            ->exists();

        if (! $isSalesmanEligible) {
            return response()->json(['success' => false, 'error' => 'Selected employee is not a salesman'], 422);
        }

        $party = Party::query()->whereKey($partyId)->first();
        if (! $party) {
            return response()->json(['success' => false, 'error' => 'Invalid party'], 422);
        }

        if ((int) $party->employee_id !== $salesmanId) {
            return response()->json(['success' => false, 'error' => 'Selected party does not belong to selected salesman'], 422);
        }

        $salesman = Employee::query()->whereKey($salesmanId)->first();
        if (! $salesman) {
            return response()->json(['success' => false, 'error' => 'Invalid salesman'], 422);
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'order_date' => $validated['order_date'],
                'salesman' => $salesman->name,
                'salesman_id' => $salesmanId,
                'party' => $party->name,
                'party_id' => $partyId,
                'bill_type' => $validated['bill_type'],
                'bill_no' => $validated['bill_no'] ?? null,
                'status' => $validated['status'],
                'type' => $validated['type'],
                'is_deleted' => false,
            ]);

            $productIds = collect($validated['items'])->pluck('product_id')->unique()->values()->all();
            $productsById = Product::query()->whereIn('id', $productIds)->get(['id', 'name'])->keyBy('id');

            foreach ($validated['items'] as $item) {
                $product = $productsById->get((int) $item['product_id']);
                $order->items()->create([
                    'product' => $product ? $product->name : 'Unknown',
                    'packing' => $item['packing'] ?? null,
                    'size' => $item['size'] ?? null,
                    'quantity' => (int) $item['quantity'],
                    'is_deleted' => false,
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'id' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'bill_type' => 'nullable|string|in:A,B',
            'bill_date' => 'nullable|date',
            'bill_no' => 'nullable|string',
            'transport_id' => 'nullable|integer|exists:transports,id',
            'status' => 'nullable|string|in:Incomplete,Finalized,Received,Okay,Cancelled',
            'existing_images' => 'nullable|string',
        ]);

        $imagePaths = json_decode($request->existing_images, true) ?? [];

        if ($request->hasFile('receiving_images')) {
            foreach ($request->file('receiving_images') as $image) {
                $path = $image->store('uploads/receiving', 'public');
                $imagePaths[] = 'storage/' . $path;
            }
        }

        return DB::transaction(function () use ($validated, $order, $imagePaths) {
            $updatePayload = $validated;

            if (array_key_exists('transport_id', $validated)) {
                $transport = $validated['transport_id']
                    ? Transport::query()->whereKey((int) $validated['transport_id'])->first()
                    : null;

                $updatePayload['transport_id'] = $transport ? $transport->id : null;
                $updatePayload['transport'] = $transport
                    ? trim($transport->name . ($transport->vehicle_number ? ' (' . $transport->vehicle_number . ')' : ''))
                    : null;
            }

            if (array_key_exists('bill_no', $updatePayload)) {
                $billNo = trim((string) ($updatePayload['bill_no'] ?? ''));
                $updatePayload['bill_no'] = $billNo === '' ? null : $billNo;
            }

            $finalBillType = (string) ($updatePayload['bill_type'] ?? $order->bill_type ?? '');
            $finalBillNo = $updatePayload['bill_no'] ?? $order->bill_no;

            if ($finalBillType === 'A' && (! $finalBillNo || trim((string) $finalBillNo) === '')) {
                $billDate = null;
                if (array_key_exists('bill_date', $updatePayload) && $updatePayload['bill_date']) {
                    $billDate = Carbon::parse($updatePayload['bill_date']);
                } elseif ($order->bill_date) {
                    $billDate = Carbon::parse($order->bill_date);
                } else {
                    $billDate = Carbon::today();
                    $updatePayload['bill_date'] = $billDate->toDateString();
                }

                $updatePayload['bill_no'] = $this->nextBillNoForOrder($order, $billDate);
            }

            $order->update(array_merge($updatePayload, ['receiving_image_path' => $imagePaths]));

            return response()->json(['success' => true, 'images' => $imagePaths, 'bill_no' => $order->bill_no]);
        });
    }

    public function destroy(Order $order)
    {
        $order->update(['is_deleted' => true]);
        $order->items()->update(['is_deleted' => true]);

        return response()->json(['success' => true]);
    }

    public function addItem(Request $request, Order $order)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'packing' => 'nullable|string',
            'size' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::query()->whereKey((int) $validated['product_id'])->first();

        $item = $order->items()->create([
            'product' => $product ? $product->name : 'Unknown',
            'packing' => $validated['packing'] ?? null,
            'size' => $validated['size'] ?? null,
            'quantity' => (int) $validated['quantity'],
            'is_deleted' => false,
        ]);

        return response()->json(['success' => true, 'item' => $item]);
    }

    public function updateItemQty(Request $request, OrderItem $item)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $item->update(['quantity' => (int) $validated['quantity']]);

        return response()->json(['success' => true]);
    }

    public function deleteItem(OrderItem $item)
    {
        $item->update(['is_deleted' => true]);

        return response()->json(['success' => true]);
    }
}
