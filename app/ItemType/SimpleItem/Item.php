<?php
declare(strict_types=1);

namespace App\ItemType\SimpleItem;

use App\ItemType\ItemType;
use App\ItemType\SimpleItem\AllowedValue\ResourceTypeItem;
use App\ItemType\SimpleItem\Response\ApiResourceTypeResponse;
use App\ItemType\SimpleItem\Response\ApiResponse;
use App\ItemType\SimpleItem\Response\ApiSummaryResourceTypeResponse;
use App\ItemType\SimpleItem\Response\ApiSummaryResponse;
use App\Transformers\Transformer;
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
        return ApiSummaryResponse::class;
    }

    public function resourceTypeSummaryClass(): string
    {
        return ApiSummaryResourceTypeResponse::class;
    }

    public function transformer(array $data_to_transform): Transformer
    {
        return new Transformers\Transformer($data_to_transform);
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
        return ApiResponse::class;
    }

    public function resourceTypeItemCollectionClass(): string
    {
        return ApiResourceTypeResponse::class;
    }

    protected function allowedValuesItemCollectionClass(): string
    {
        return AllowedValue\Item::class;
    }

    protected function allowedValuesResourceTypeItemCollectionClass(): string
    {
        return ResourceTypeItem::class;
    }
}
