<?php
declare(strict_types=1);

namespace App\Models\Transformers;

use App\Models\SubCategory as SubCategoryModel;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * This is an updated version of the transformers, the other transformers need to
 * be updated to operate on an array rather than collections
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends Transformer
{
    protected $data_to_transform;

    private $parameters = [];

    private $subcategories = [];

    /**
     * ResourceType constructor.
     *
     * @param array $data_to_transform
     * @param array $parameters
     */
    public function __construct(array $data_to_transform, array $parameters = [])
    {
        parent::__construct();

        $this->data_to_transform = $data_to_transform;
        $this->parameters = $parameters;
    }

    public function toArray(): array
    {
        $result = [
            'id' => $this->hash->category()->encode($this->data_to_transform['category_id']),
            'name' => $this->data_to_transform['category_name'],
            'description' => $this->data_to_transform['category_description'],
            'created' => $this->data_to_transform['category_created_at'],
            'resource_type' => [
                'id' => $this->hash->resourceType()->encode($this->data_to_transform['resource_type_id']),
                'name' => $this->data_to_transform['resource_type_name'],
            ],
            'subcategories_count' => $this->data_to_transform['category_sub_categories']
        ];

        if (
            isset($this->parameters['include-subcategories']) &&
            $this->parameters['include-subcategories'] === true
        ) {
            $subCategoriesCollection = (new SubCategoryModel())->paginatedCollection(
                $this->data_to_transform['category_id']
            );

            $subCategoriesCollection->map(
                function ($sub_category) {
                    $this->subcategories[] = (new SubCategory($sub_category))->toArray();
                }
            );

            $result['subcategories'] = $this->subcategories;
        }

        return $result;
    }
}
