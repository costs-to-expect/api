<?php

namespace App\Transformers;

use App\Models\Category as CategoryModel;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends Transformer
{
    private $category;
    private $parameters = [];

    private $sub_categories = [];

    /**
     * ResourceType constructor.
     *
     * @param CategoryModel $category
     * @param array $parameters
     */
    public function __construct(CategoryModel $category, array $parameters = [])
    {
        parent::__construct();

        $this->category = $category;
        $this->parameters = $parameters;
    }

    public function toArray(): array
    {
        $result = [
            'id' => $this->hash->category()->encode($this->category->id),
            'name' => $this->category->name,
            'description' => $this->category->description,
            'created' => $this->category->created_at->toDateTimeString(),
            'sub_categories_count' => $this->category->sub_categories_count()
        ];

        if (
            isset($this->parameters['include_sub_categories']) &&
            $this->parameters['include_sub_categories'] === true
        ) {
            $subCategoriesCollection = $this->category->sub_categories;

            $subCategoriesCollection->map(
                function ($sub_category) {
                    $this->sub_categories[] = (new SubCategory($sub_category))->toArray();
                }
            );

            $result['sub_categories'] = $this->sub_categories;
        }

        return $result;
    }
}
