<?php
declare(strict_types=1);

namespace App\Models\Transformers\Item;

use App\Models\Transformers\Transformer;

/**
 * Transform the data from our queries into the format we want to display
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SimpleExpense extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->item()->encode($to_transform['item_id']),
            'name' => $to_transform['item_name'],
            'description' => $to_transform['item_description'],
            'currency' => [
                'id' => $this->hash->currency()->encode($to_transform['item_currency_id']),
                'code' => $to_transform['item_currency_code'],
                'name' => $to_transform['item_currency_name'],
            ],
            'total' => number_format((float) $to_transform['item_total'],2, '.', ''),
            'categories' => [],
            'created' => $to_transform['item_created_at'],
            'updated' => $to_transform['item_updated_at']
        ];

        if (
            array_key_exists('category_id', $to_transform) === true &&
            array_key_exists('category_name', $to_transform) === true &&
            array_key_exists('category_description', $to_transform) === true &&
            array_key_exists('item_category_id', $to_transform) === true
        ) {
            if ($to_transform['category_id'] !== null) {
                $category = [
                    'id' => $this->hash->itemCategory()->encode($to_transform['item_category_id']),
                    'category_id' => $this->hash->category()->encode($to_transform['category_id']),
                    'name' => $to_transform['category_name'],
                    'description' => $to_transform['category_description'],
                    "subcategories" => []
                ];
            }

            if (
                array_key_exists('subcategory_id', $to_transform) === true &&
                array_key_exists('subcategory_name', $to_transform) === true &&
                array_key_exists('subcategory_description', $to_transform) === true &&
                array_key_exists('item_subcategory_id', $to_transform) === true
            ) {
                if ($to_transform['subcategory_id'] !== null) {
                    $category['subcategories'][] = [
                        'id' => $this->hash->itemSubcategory()->encode($to_transform['item_subcategory_id']),
                        'subcategory_id' => $this->hash->subcategory()->encode($to_transform['subcategory_id']),
                        'name' => $to_transform['subcategory_name'],
                        'description' => $to_transform['subcategory_description']
                    ];
                }
            }

            $this->transformed['categories'][] = $category;
        }
    }
}
