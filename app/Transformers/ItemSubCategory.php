<?php

namespace App\Transformers;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubCategory extends Transformer
{
    protected $item_sub_category;

    public function __construct(\App\Models\ItemSubCategory $item_sub_category)
    {
        parent::__construct();

        $this->item_sub_category = $item_sub_category;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->hash->encode($this->item_sub_category->id),
            'sub_category' => [
                'name' => $this->item_sub_category->sub_category->name,
                'description' => $this->item_sub_category->sub_category->description
            ],
            'created' => $this->item_sub_category->created_at->toDateTimeString()
        ];
    }
}
