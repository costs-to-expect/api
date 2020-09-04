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

    public function type(): string
    {
        return 'simple-item';
    }
}
