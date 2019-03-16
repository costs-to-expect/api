<?php
declare(strict_types=1);

namespace App\Transformers;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategory extends Transformer
{
    protected $item_category;

    public function __construct(\App\Models\ItemCategory $item_category)
    {
        parent::__construct();

        $this->item_category = $item_category;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->hash->itemCategory()->encode($this->item_category->id),
            'category' => [
                'name' => $this->item_category->category->name,
                'description' => $this->item_category->category->description
            ],
            'created' => $this->item_category->created_at->toDateTimeString()
        ];
    }
}
