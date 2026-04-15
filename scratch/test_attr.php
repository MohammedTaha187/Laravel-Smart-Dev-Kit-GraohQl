<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use App\Traits\HandlesAttributeConfig;

#[Guarded(['id'])]
class TestModel extends Model {
    use HandlesAttributeConfig;
}

$model = new TestModel();

echo "Attribute Configured Guarded: " . json_encode($model->getGuarded()) . "\n";

if ($model->getGuarded() === ['id']) {
    echo "SUCCESS: Polyfill is working!\n";
} else {
    echo "FAILURE: Polyfill not working.\n";
}
