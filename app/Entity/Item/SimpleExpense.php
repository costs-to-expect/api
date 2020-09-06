<?php
declare(strict_types=1);

namespace App\Entity\Item;

use App\Entity\Config;

class SimpleExpense extends Config
{
    public function __construct()
    {
        $this->base_path = 'api.item-type-simple-expense';

        parent::__construct();
    }

    public function table(): string
    {
        return 'item_type_simple_expense';
    }

    public function type(): string
    {
        return 'simple-expense';
    }
}
