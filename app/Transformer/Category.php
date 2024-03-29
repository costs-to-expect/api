<?php

declare(strict_types=1);

namespace App\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends Transformer
{
    public function format(array $to_transform): void
    {
        $category_id = $this->hash->category()->encode($to_transform['category_id']);
        $resource_type_id = $this->hash->resourceType()->encode($to_transform['resource_type_id']);

        $this->transformed = [
            'id' => $category_id,
            'name' => $to_transform['category_name'],
            'description' => $to_transform['category_description'],
            'created' => $to_transform['category_created_at'],
            'resource_type' => [
                'uri' => route('resource-type.show', ['resource_type_id' => $resource_type_id], false),
                'id' => $resource_type_id,
                'name' => $to_transform['resource_type_name'],
            ]
        ];

        if (array_key_exists('category_subcategories', $to_transform)) {
            $this->transformed['subcategories']['uri'] = route('subcategory.list', ['resource_type_id' => $resource_type_id, 'category_id' => $category_id], false);
            $this->transformed['subcategories']['count'] = $to_transform['category_subcategories'];
        }

        if (array_key_exists('subcategories', $this->related) === true) {
            foreach ($this->related['subcategories'] as $subcategory) {
                $this->transformed['subcategories']['collection'][] = (new Subcategory($subcategory))->asArray();
            }
        }
    }
}
