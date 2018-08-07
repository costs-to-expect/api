<?php

namespace App\Transformers;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Item extends Transformer
{
    protected $item;

    public function __construct(\App\Models\Item $item)
    {
        parent::__construct();

        $this->item = $item;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->hash->item()->encode($this->item->id),
            'description' => $this->item->description,
            'total' => number_format($this->item->total, 2),
            'percentage' => $this->item->percentage,
            'actualised_total' => number_format($this->item->actualised_total, 2),
            'effective_date' => $this->item->effective_date,
            'created' => $this->item->created_at->toDateTimeString()
        ];
    }
}
