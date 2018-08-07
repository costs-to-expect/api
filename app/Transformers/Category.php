<?php

namespace App\Transformers;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends Transformer
{
    protected $category;

    public function __construct(\App\Models\Category $category)
    {
        parent::__construct();

        $this->category = $category;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->hash->category()->encode($this->category->id),
            'name' => $this->category->name,
            'description' => $this->category->description,
            'created' => $this->category->created_at->toDateTimeString(),
            'sub_categories_count' => $this->category->numberOfSubCategories()
        ];
    }
}
