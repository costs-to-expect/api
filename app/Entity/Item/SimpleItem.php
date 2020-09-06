<?php
declare(strict_types=1);

namespace App\Entity\Item;

use App\Models\Transformers\Transformer;
use Illuminate\Database\Eloquent\Model;

class SimpleItem extends Config
{
    public function __construct()
    {
        $this->base_path = 'api.item-type-simple-item';

        parent::__construct();
    }

    public function table(): string
    {
        return 'item_type_simple_item';
    }

    public function type(): string
    {
        return 'simple-item';
    }

    public function model()
    {
        return new \App\Models\Item\SimpleItem();
    }

    public function transformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\ItemType\SimpleItem($data_to_transform);
    }
}
