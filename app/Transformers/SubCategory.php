<?php

namespace App\Transformers;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubCategory extends Transformer
{
    protected $sub_category;

    public function __construct(\App\Models\SubCategory $sub_category)
    {
        parent::__construct();

        $this->sub_category = $sub_category;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->hash->encode($this->sub_category->id),
            'name' => $this->sub_category->name,
            'description' => $this->sub_category->description,
            'created' => $this->sub_category->created_at->toDateTimeString()
        ];
    }
}
