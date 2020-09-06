<?php
declare(strict_types=1);

namespace App\Entity\Item;

use App\Models\Transformers\Transformer;
use Illuminate\Database\Eloquent\Model;

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

    public function model(): Model
    {
        return new \App\Models\Item\AllocatedExpense();
    }

    public function transformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\ItemType\AllocatedExpense($data_to_transform);
    }
}
