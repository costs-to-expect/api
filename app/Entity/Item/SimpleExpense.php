<?php
declare(strict_types=1);

namespace App\Entity\Item;

use App\Models\Transformers\Transformer;
use App\Request\Validate\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class SimpleExpense extends Config
{
    public function __construct()
    {
        $this->base_path = 'api.item-type-simple-expense';

        parent::__construct();
    }

    public function create(int $id): Model
    {
        $item = new \App\Models\Item\SimpleExpense([
            'item_id' => $id,
            'name' => request()->input('name'),
            'description' => request()->input('description', null),
            'total' => request()->input('total'),
            'created_at' => Date::now(),
            'updated_at' => null
        ]);

        $item->save();

        return $item;
    }

    public function model()
    {
        return new \App\Models\Item\SimpleExpense();
    }

    public function table(): string
    {
        return 'item_type_simple_expense';
    }

    public function type(): string
    {
        return 'simple-expense';
    }

    public function transformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\ItemType\SimpleExpense($data_to_transform);
    }

    public function validator(): Validator
    {
        return new \App\Request\Validate\ItemType\SimpleExpense();
    }
}
