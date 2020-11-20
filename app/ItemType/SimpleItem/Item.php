<?php
declare(strict_types=1);

namespace App\ItemType\SimpleItem;

use App\ItemType\ItemType;
use App\Models\Transformers\Transformer;
use App\Request\Validate\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class Item extends ItemType
{
    public function __construct()
    {
        $this->base_path = 'api.item-type-simple-item';

        $this->resource_type_base_path = 'api.resource-type-item-type-simple-item';

        parent::__construct();
    }

    public function allowedValuesForItem(int $resource_type_id): array
    {
        return [];
    }

    public function categoryAssignmentLimit(): int
    {
        return 0;
    }

    public function create(int $id): Model
    {
        $item = new \App\ItemType\SimpleItem\Model([
            'item_id' => $id,
            'name' => request()->input('name'),
            'description' => request()->input('description', null),
            'quantity' => request()->input('quantity', 1),
            'created_at' => Date::now(),
            'updated_at' => null
        ]);

        $item->save();

        return $item;
    }

    public function instance(int $id): Model
    {
        return (new \App\ItemType\SimpleItem\Model())->instance($id);
    }

    public function model()
    {
        return new \App\ItemType\SimpleItem\Model();
    }

    public function subcategoryAssignmentLimit(): int
    {
        return 0;
    }

    public function table(): string
    {
        return 'item_type_simple_item';
    }

    public function type(): string
    {
        return 'simple-item';
    }

    public function summaryClass(): string
    {
        return \App\Http\Controllers\Summary\Item\SimpleItem::class;
    }

    public function resourceTypeSummaryClass(): string
    {
        return \App\Http\Controllers\Summary\ResourceTypeItem\SimpleItem::class;
    }

    public function transformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\Item\SimpleItem($data_to_transform);
    }

    public function update(array $patch, Model $instance): bool
    {
        foreach ($patch as $key => $value) {
            $instance->$key = $value;
        }

        $instance->updated_at = Date::now();

        return $instance->save();
    }

    public function validator(): Validator
    {
        return new \App\ItemType\SimpleItem\Validator();
    }

    public function viewClass(): string
    {
        return \App\ItemType\SimpleItem\Response::class;
    }

    public function resourceTypeItemCollectionClass(): string
    {
        return \App\Http\Controllers\ResourceTypeItem\SimpleItem::class;
    }

    protected function allowedValuesItemCollectionClass(): string
    {
        return \App\AllowedValue\Item\SimpleItem::class;
    }

    protected function allowedValuesResourceTypeItemCollectionClass(): string
    {
        return \App\AllowedValue\ResourceTypeItem\SimpleItem::class;
    }
}
