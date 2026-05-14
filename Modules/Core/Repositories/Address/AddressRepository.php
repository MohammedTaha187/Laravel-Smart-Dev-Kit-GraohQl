<?php

namespace Modules\Core\Repositories\Address;

use Modules\Core\Repositories\Address\Contracts\AddressRepositoryInterface;
use EasyDev\Laravel\Repositories\BaseRepository;
use Modules\Core\Models\Address;

class AddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    public function __construct(Address $model)
    {
        parent::__construct($model);
    }
}
