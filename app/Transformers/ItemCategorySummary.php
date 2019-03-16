<?php

namespace App\Transformers;

use App\Models\Item as ItemModel;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategorySummary extends Transformer
{
    private $category_summary;

    /**
     * ResourceType constructor.
     *
     * @param ItemModel $category_summary
     */
    public function __construct(ItemModel $category_summary)
    {
        parent::__construct();

        $this->category_summary = $category_summary;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->hash->category()->encode($this->category_summary->id),
            'name' => $this->category_summary->name,
            'total' => number_format($this->category_summary->total, 2, '.', '')
        ];
    }
}
