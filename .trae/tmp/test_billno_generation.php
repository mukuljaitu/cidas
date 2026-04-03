<?php

require __DIR__.'/../../vendor/autoload.php';

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\OrderController;
use App\Models\Order;
use Illuminate\Http\Request;

$controller = app()->make(OrderController::class);

$o1 = Order::query()->create([
    'order_date' => date('Y-m-d'),
    'salesman' => 'Test Salesman',
    'party' => 'Test Party',
    'bill_type' => 'A',
    'bill_no' => null,
    'status' => 'Incomplete',
    'type' => 'Fer',
    'is_deleted' => false,
]);

$r1 = Request::create('/orders/'.$o1->id, 'POST', [
    'bill_type' => 'A',
    'bill_date' => '2026-04-03',
    'status' => 'Finalized',
    'existing_images' => '[]',
]);
$r1->setLaravelSession(app('session')->driver());

$resp1 = $controller->update($r1, $o1);

$o2 = Order::query()->create([
    'order_date' => date('Y-m-d'),
    'salesman' => 'Test Salesman',
    'party' => 'Test Party',
    'bill_type' => 'A',
    'bill_no' => null,
    'status' => 'Incomplete',
    'type' => 'Fer',
    'is_deleted' => false,
]);

$r2 = Request::create('/orders/'.$o2->id, 'POST', [
    'bill_type' => 'A',
    'bill_date' => '2026-04-03',
    'status' => 'Finalized',
    'existing_images' => '[]',
]);
$r2->setLaravelSession(app('session')->driver());

$resp2 = $controller->update($r2, $o2);

echo "Order1 bill_no: ".$o1->fresh()->bill_no.PHP_EOL;
echo "Order2 bill_no: ".$o2->fresh()->bill_no.PHP_EOL;

