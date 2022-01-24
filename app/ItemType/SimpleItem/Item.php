<?php
declare(strict_types=1);

namespace App\ItemType\SimpleItem;

use App\ItemType\ItemType;
use App\Transformers\Transformer;
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

    public function create(int $id): Model
    {
        $item = new Models\Item([
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
        return (new Models\Item())->instance($id);
    }

    public function model()
    {
        return new Models\Item();
    }

    public function table(): string
    {
        return 'item_type_simple_item';
    }

    public function type(): string
    {
        return 'simple-item';
    }

    public function apiSummaryResponseClass(): string
    {
        return ApiResponse\Summary::class;
    }

    public function apiSummaryResourceTypeItemResponseClass(): string
    {
        return ApiResponse\SummaryResourceTypeItem::class;
    }

    public function transformer(array $data_to_transform): Transformer
    {
        return new Transformers\Item($data_to_transform);
    }

    public function update(array $patch, Model $instance): bool
    {
        foreach ($patch as $key => $value) {
            $instance->$key = $value;
        }

        $instance->updated_at = Date::now();

        return $instance->save();
    }

    public function apiItemResponseClass(): string
    {
        return ApiResponse\Item::class;
    }

    public function apiResourceTypeItemResponseClass(): string
    {
        return ApiResponse\ResourceTypeItem::class;
    }

    protected function allowedValuesItemCollectionClass(): string
    {
        return AllowedValue\Item::class;
    }

    protected function allowedValuesResourceTypeItemCollectionClass(): string
    {
        return AllowedValue\ResourceTypeItem::class;
    }
}
