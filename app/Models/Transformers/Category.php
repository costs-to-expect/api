<?php
declare(strict_types=1);

namespace App\Models\Transformers;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * This is an updated version of the transformers, the other transformers need to
 * be updated to operate on an array rather than collections
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends Transformer
{
    private $data_to_transform;

    private $subcategories = [];

    /**
     * ResourceType constructor.
     *
     * @param array $data_to_transform
     * @param array $subcategories
     */
    public function __construct(array $data_to_transform, array $subcategories = [])
    {
        parent::__construct();

        $this->data_to_transform = $data_to_transform;
        $this->subcategories = $subcategories;
    }

    public function toArray(): array
    {
        $result = [
            'id' => $this->hash->category()->encode($this->data_to_transform['category_id']),
            'name' => $this->data_to_transform['category_name'],
            'description' => $this->data_to_transform['category_description'],
            'created' => $this->data_to_transform['category_created_at'],
            'resource_type' => [
                'id' => $this->hash->resourceType()->encode($this->data_to_transform['resource_type_id'])
            ]
        ];

        if (array_key_exists('resource_type_name', $this->data_to_transform) === true) {
           $result['resource_type']['name'] = $this->data_to_transform['resource_type_name'];
        }

        if (array_key_exists('category_subcategories', $this->data_to_transform)) {
            $result['subcategories']['count'] = $this->data_to_transform['category_subcategories'];
        }

        foreach ($this->subcategories as $subcategory) {
            $result['subcategories']['collection'][] = (new SubCategory($subcategory))->toArray();
        }

        return $result;
    }
}
