<?php
declare(strict_types=1);

namespace App\ItemType\AllocatedExpense;

use App\HttpRequest\Hash;
use App\ItemType\ItemType;
use App\Models\AllowedValue\Currency;
use App\Transformer\Transformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class Item extends ItemType
{
    public function __construct()
    {
        $this->base_path = 'api.item-type-allocated-expense';

        $this->resource_type_base_path = 'api.resource-type-item-type-allocated-expense';

        parent::__construct();
    }

    public function allowedValuesForItem(int $resource_type_id): array
    {
        return (new Currency())->allowedValues();
    }

    public function create($id): Model
    {
        $hash = new Hash();
        $currency_id = $hash->decode('currency', request()->input('currency_id'));

        $item = new Models\Item([
            'item_id' => $id,
            'name' => request()->input('name'),
            'description' => request()->input('description', null),
            'effective_date' => request()->input('effective_date'),
            'publish_after' => request()->input('publish_after', null),
            'currency_id' => $currency_id,
            'total' => request()->input('total'),
            'percentage' => request()->input('percentage', 100),
            'created_at' => Date::now(),
            'updated_at' => null
        ]);

        $item->setActualisedTotal(
            request()->input('total'),
            request()->input('percentage', 100)
        );

        $item->save();

        return $item;
    }

    public function instance(int $id): Model
    {
        return (new Models\Item())->instance($id);
    }

    public function table(): string
    {
        return 'item_type_allocated_expense';
    }

    public function type(): string
    {
        return 'allocated-expense';
    }

    public function model()
    {
        return new Models\Item();
    }

    public function transformer(array $data_to_transform): Transformer
    {
        return new Transformer\Item($data_to_transform);
    }

    public function update(array $patch, Model $instance): bool
    {
        $set_actualised = false;
        foreach ($patch as $key => $value) {
            $instance->$key = $value;

            if (in_array($key, ['total', 'percentage']) === true) {
                $set_actualised = true;
            }

            if ($key === 'currency_id') {
                $hash = new Hash();
                $instance->$key = $hash->decode('currency', request()->input('currency_id'));
            }
        }

        if ($set_actualised === true) {
            $instance->setActualisedTotal($instance->total, $instance->percentage);
        }

        $instance->updated_at = Date::now();

        return $instance->save();
    }

    protected function allowedValuesItemCollectionClass(): string
    {
        return Models\AllowedValue\Item::class;
    }

    protected function allowedValuesResourceTypeItemCollectionClass(): string
    {
        return Models\AllowedValue\ResourceTypeItem::class;
    }
}
