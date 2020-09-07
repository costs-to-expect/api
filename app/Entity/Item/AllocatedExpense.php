<?php
declare(strict_types=1);

namespace App\Entity\Item;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transformers\Transformer;
use App\Request\Validate\Validator;
use Illuminate\Support\Facades\Date;

class AllocatedExpense extends Item
{
    public function __construct()
    {
        $this->base_path = 'api.item-type-allocated-expense';

        parent::__construct();
    }

    /**
     * Create and save the item and item type data
     *
     * @param integer $id
     *
     * @return Model
     */
    public function create($id): Model
    {
        $item = new \App\Models\Item\AllocatedExpense([
            'item_id' => $id,
            'name' => request()->input('name'),
            'description' => request()->input('description', null),
            'effective_date' => request()->input('effective_date'),
            'publish_after' => request()->input('publish_after', null),
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

    public function dateRangeField(): ?string
    {
        return 'effective_date';
    }

    public function instance(int $id): Model
    {
        return (new \App\Models\Item\AllocatedExpense())->instance($id);
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
        return new \App\Models\Item\AllocatedExpense();
    }

    public function transformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\ItemType\AllocatedExpense($data_to_transform);
    }

    public function update(array $patch, Model $instance): bool
    {
        $set_actualised = false;
        foreach ($patch as $key => $value) {
            $instance->$key = $value;

            if (in_array($key, ['total', 'percentage']) === true) {
                $set_actualised = true;
            }
        }

        if ($set_actualised === true) {
            $instance->setActualisedTotal($instance->total, $instance->percentage);
        }

        $instance->updated_at = Date::now();

        return $instance->save();
    }

    public function validator(): Validator
    {
        return new \App\Request\Validate\ItemType\AllocatedExpense();
    }
}
