<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
// avoid warnings if model missing
if (!class_exists(\App\Models\Contact::class)) {
    echo "Contact model not found\n";
    exit(1);
}
$count = \App\Models\Contact::count();
echo "Count: {$count}\n";
$rows = \App\Models\Contact::limit(10)->get(['id','first_name','last_name','email'])->toArray();
print_r($rows);
