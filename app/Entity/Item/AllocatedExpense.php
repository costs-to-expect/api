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

    public function dateRangeField(): ?string
    {
        return 'effective_date';
    }

    public function table(): string
    {
        return 'item_type_allocated_expense';
    }

    public function type(): string
    {
        return 'allocated-expense';
    }
}
