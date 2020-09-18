<?php
declare(strict_types=1);

namespace App\Entity\Item;

use App\Models\Transformers\Transformer;
use App\Request\Validate\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class SimpleItem extends Item
{
    public function __construct()
    {
        $this->base_path = 'api.item-type-simple-item';

        $this->resource_type_base_path = 'api.resource-type-item-type-simple-item';

        parent::__construct();
    }

    public function create(int $id): Model
    {
        $item = new \App\Models\Item\SimpleItem([
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
        return (new \App\Models\Item\SimpleItem())->instance($id);
    }

    public function model()
    {
        return new \App\Models\Item\SimpleItem();
    }

    public function summaryModel(): Model
    {
        return new \App\Models\Item\Summary\SimpleItem();
    }

    public function table(): string
    {
        return 'item_type_simple_item';
    }

    public function type(): string
    {
        return 'simple-item';
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
        return new \App\Request\Validate\ItemType\SimpleItem();
    }

    public function resourceTypeModel(): Model
    {
        return new \App\Models\ResourceTypeItem\SimpleItem();
    }

    public function resourceTypeTransformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\ResourceTypeItemType\SimpleItem($data_to_transform);
    }

    public function summaryResourceTypeModel(): Model
    {
        return new \App\Models\ResourceTypeItem\Summary\SimpleItem();
    }
}
