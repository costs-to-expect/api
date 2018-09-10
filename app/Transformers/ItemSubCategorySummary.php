<?php

namespace App\Transformers;

use App\Models\Item as ItemModel;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubCategorySummary extends Transformer
{
    private $sub_category_summary;

    /**
     * ResourceType constructor.
     *
     * @param ItemModel $sub_category_summary
     */
    public function __construct(ItemModel $sub_category_summary)
    {
        parent::__construct();

        $this->sub_category_summary = $sub_category_summary;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->hash->subCategory()->encode($this->sub_category_summary->id),
            'name' => $this->sub_category_summary->name,
            'total' => number_format($this->sub_category_summary->total, 2, '.', '')
        ];
    }
}
