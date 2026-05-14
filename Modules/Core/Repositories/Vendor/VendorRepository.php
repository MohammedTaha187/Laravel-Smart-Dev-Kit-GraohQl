<?php

namespace Modules\Core\Repositories\Vendor;

use Modules\Core\Repositories\Vendor\Contracts\VendorRepositoryInterface;
use EasyDev\Laravel\Repositories\BaseRepository;
use Modules\Core\Models\Vendor;

class VendorRepository extends BaseRepository implements VendorRepositoryInterface
{
    public function __construct(Vendor $model)
    {
        parent::__construct($model);
    }
}
