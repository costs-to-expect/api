<?php
declare(strict_types=1);

namespace App\Transformers;

/**
 * Transform the data from our queries into the format we want to display
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->category()->encode($to_transform['category_id']),
            'name' => $to_transform['category_name'],
            'description' => $to_transform['category_description'],
            'created' => $to_transform['category_created_at'],
            'resource_type' => [
                'id' => $this->hash->resourceType()->encode($to_transform['resource_type_id'])
            ]
        ];

        if (array_key_exists('resource_type_name', $to_transform) === true) {
           $this->transformed['resource_type']['name'] = $to_transform['resource_type_name'];
        }

        if (array_key_exists('category_subcategories', $to_transform)) {
            $this->transformed['subcategories']['count'] = $to_transform['category_subcategories'];
        }

        if (array_key_exists('subcategories', $this->related) === true) {
            foreach ($this->related['subcategories'] as $subcategory) {
                $this->transformed['subcategories']['collection'][] = (new Subcategory($subcategory))->asArray();
            }
        }
    }
}
