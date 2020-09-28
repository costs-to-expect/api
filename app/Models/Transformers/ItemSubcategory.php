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
class ItemSubcategory extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->itemSubcategory()->encode($to_transform['item_sub_category_id']),
            'subcategory' => [
                'id' => $this->hash->subcategory()->encode($to_transform['item_sub_category_sub_category_id']),
                'name' => $to_transform['item_sub_category_sub_category_name'],
                'description' => $to_transform['item_sub_category_sub_category_description']
            ],
            'created' => $to_transform['item_sub_category_created_at']
        ];
    }
}
