<?php
declare(strict_types=1);

namespace App\Entity\Item;

use App\Request\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transformers\Transformer;
use App\Request\Validate\Validator;
use Illuminate\Support\Facades\Date;

class AllocatedExpense extends Item
{
    public function __construct()
    {
        $this->base_path = 'api.item-type-allocated-expense';

        $this->resource_type_base_path = 'api.resource-type-item-type-allocated-expense';

        parent::__construct();
    }

    public function allowedValuesForItem(int $resource_type_id): array
    {
        return (new \App\Option\AllowedValue\Currency())->allowedValues();
    }

    public function allowPartialTransfers(): bool
    {
        return true;
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
        $hash = new Hash();
        $currency_id = $hash->decode('currency', request()->input('currency_id'));

        $item = new \App\Models\Item\AllocatedExpense([
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

    public function summaryClass(): string
    {
        return \App\Http\Controllers\Summary\Item\AllocatedExpense::class;
    }

    public function resourceTypeSummaryClass(): string
    {
        return \App\Http\Controllers\Summary\ResourceTypeItem\AllocatedExpense::class;
    }

    public function summaryModel(): Model
    {
        return new \App\Models\Item\Summary\AllocatedExpense();
    }

    public function summaryTransformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\Item\Summary\ExpenseItem($data_to_transform);
    }

    public function summaryTransformerByCategory(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\Item\Summary\ExpenseItemByCategory($data_to_transform);
    }

    public function summaryTransformerBySubcategory(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\Item\Summary\ExpenseItemBySubcategory($data_to_transform);
    }

    public function summaryTransformerByMonth(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\Item\Summary\ExpenseItemByMonth($data_to_transform);
    }

    public function summaryTransformerByYear(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\Item\Summary\ExpenseItemByYear($data_to_transform);
    }

    public function summaryTransformerByResource(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\Item\Summary\ExpenseItemByResource($data_to_transform);
    }

    public function transformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\Item\AllocatedExpense($data_to_transform);
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

    public function validator(): Validator
    {
        return new \App\Request\Validate\ItemType\AllocatedExpense();
    }

    public function resourceTypeModel(): Model
    {
        return new \App\Models\ResourceTypeItem\AllocatedExpense();
    }

    public function resourceTypeTransformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\ResourceTypeItem\AllocatedExpense($data_to_transform);
    }

    public function summaryResourceTypeModel(): Model
    {
        return new \App\Models\ResourceTypeItem\Summary\AllocatedExpense();
    }

    protected function allowedValuesItemCollectionClass(): string
    {
        return \App\Option\AllowedValue\Item\AllocatedExpense::class;
    }

    protected function allowedValuesResourceTypeItemCollectionClass(): string
    {
        return \App\Option\AllowedValue\ResourceTypeItem\AllocatedExpense::class;
    }
}
