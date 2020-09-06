<?php
declare(strict_types=1);

namespace App\Entity\Item;

use App\Models\Transformers\Transformer;
use Illuminate\Database\Eloquent\Model;

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

    public function model(): Model
    {
        return new \App\Models\Item\SimpleExpense();
    }

    public function transformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\ItemType\SimpleExpense($data_to_transform);
    }
}
