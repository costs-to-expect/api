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
}
