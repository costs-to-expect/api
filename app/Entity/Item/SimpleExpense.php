<?php
declare(strict_types=1);

namespace App\Entity\Item;

use App\Models\Transformers\Transformer;
use App\Request\Hash;
use App\Request\Validate\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class SimpleExpense extends Item
{
    public function __construct()
    {
        $this->base_path = 'api.item-type-simple-expense';

        $this->resource_type_base_path = 'api.resource-type-item-type-simple-expense';

        parent::__construct();
    }

    public function allowedValuesForItem(int $resource_type_id): array
    {
        return (new \App\AllowedValue\Currency())->allowedValues();
    }

    public function create(int $id): Model
    {
        $hash = new Hash();
        $currency_id = $hash->decode('currency', request()->input('currency_id'));

        $item = new \App\Models\Item\SimpleExpense([
            'item_id' => $id,
            'name' => request()->input('name'),
            'description' => request()->input('description', null),
            'currency_id' => $currency_id,
            'total' => request()->input('total'),
            'created_at' => Date::now(),
            'updated_at' => null
        ]);

        $item->save();

        return $item;
    }

    public function instance(int $id): Model
    {
        return (new \App\Models\Item\SimpleExpense())->instance($id);
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

    public function summaryClass(): string
    {
        return \App\Http\Controllers\Summary\Item\SimpleExpense::class;
    }

    public function resourceTypeSummaryClass(): string
    {
        return \App\Http\Controllers\Summary\ResourceTypeItem\SimpleExpense::class;
    }

    public function transformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\Item\SimpleExpense($data_to_transform);
    }

    public function update(array $patch, Model $instance): bool
    {
        foreach ($patch as $key => $value) {
            $instance->$key = $value;

            if ($key === 'currency_id') {
                $hash = new Hash();
                $instance->$key = $hash->decode('currency', request()->input('currency_id'));
            }
        }

        $instance->updated_at = Date::now();

        return $instance->save();
    }

    public function validator(): Validator
    {
        return new \App\Request\Validate\ItemType\SimpleExpense();
    }

    public function resourceTypeModel(): Model
    {
        return new \App\Models\ResourceTypeItem\SimpleExpense();
    }

    public function resourceTypeTransformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\ResourceTypeItem\SimpleExpense($data_to_transform);
    }

    public function resourceTypeItemCollectionClass(): string
    {
        return \App\Http\Controllers\ResourceTypeItem\SimpleExpense::class;
    }

    protected function allowedValuesItemCollectionClass(): string
    {
        return \App\AllowedValue\Item\SimpleExpense::class;
    }

    protected function allowedValuesResourceTypeItemCollectionClass(): string
    {
        return \App\AllowedValue\ResourceTypeItem\SimpleExpense::class;
    }
}
