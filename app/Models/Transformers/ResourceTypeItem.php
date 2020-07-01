<?php
declare(strict_types=1);

namespace App\Models\Transformers;

/**
 * Transform the data from our queries into the format we want to display
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItem extends Transformer
{
    protected function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->item()->encode($to_transform['item_id']),
            'name' => $to_transform['item_name'],
            'description' => $to_transform['item_description'],
            'total' => number_format((float) $to_transform['item_total'], 2, '.', ''),
            'percentage' => (int) $to_transform['item_percentage'],
            'actualised_total' => number_format((float) $to_transform['item_actualised_total'], 2, '.', ''),
            'effective_date' => $to_transform['item_effective_date'],
            'created' => $to_transform['item_created_at'],
            'resource' => [
                'id' => $this->hash->resource()->encode($to_transform['resource_id']),
                'name' => $to_transform['resource_name'],
                'description' => $to_transform['resource_description']
            ]
        ];

        if (
            array_key_exists('category_id', $to_transform) === true &&
            array_key_exists('category_name', $to_transform) === true
        ) {
            $item['category'] = [
                'id' => $this->hash->category()->encode($to_transform['category_id']),
                'name' => $to_transform['category_name'],
                'description' => $to_transform['category_description']
            ];

            if (
                array_key_exists('subcategory_id', $to_transform) === true &&
                array_key_exists('subcategory_name', $to_transform) === true
            ) {
                $item['subcategory'] = [
                    'id' => $this->hash->subCategory()->encode($to_transform['subcategory_id']),
                    'name' => $to_transform['subcategory_name'],
                    'description' => $to_transform['subcategory_description']
                ];
            }
        }
    }
}
