<?php
declare(strict_types=1);

namespace App\Entity\Item;

use App\Entity\Config;

class SimpleItem extends Config
{
    public function __construct()
    {
        $this->base_path = 'api.item-type-simple-item';

        parent::__construct();
    }

    public function table(): string
    {
        return 'item_type_simple_item';
    }

    public function type(): string
    {
        return 'simple-item';
    }
}
