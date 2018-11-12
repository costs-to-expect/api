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
class CategorySubCategorySummary extends Transformer
{
    private $summary;

    /**
     * CategorySubCategorySummary constructor.
     *
     * @param ItemModel $summary
     */
    public function __construct(ItemModel $summary)
    {
        parent::__construct();

        $this->summary = $summary;
    }

    public function toArray(): array
    {
        return [
            'category' => $this->summary->category,
            'sub_category' => $this->summary->sub_category,
            'total' => number_format($this->summary->actualised_total, 2, '.', ''),
            'items' => $this->summary->items
        ];
    }
}
