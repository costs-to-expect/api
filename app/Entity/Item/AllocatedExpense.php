<?php
declare(strict_types=1);

namespace App\Entity\Item;

use App\Entity\Config;

class AllocatedExpense extends Config
{
    public function __construct()
    {
        $this->base_path = 'api.item-type-allocated-expense';

        parent::__construct();
    }

    public function type(): string
    {
        return 'allocated-expense';
    }
}
