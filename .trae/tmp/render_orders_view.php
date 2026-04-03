<?php

$autoload = __DIR__.'/../../vendor/autoload.php';
if (file_exists($autoload)) {
    require $autoload;
}

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo view('orders.index')->render();
