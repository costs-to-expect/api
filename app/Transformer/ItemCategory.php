<?php

declare(strict_types=1);

namespace App\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategory extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->itemCategory()->encode($to_transform['item_category_id']),
            'category' => [
                'id' => $this->hash->category()->encode($to_transform['item_category_category_id']),
                'name' => $to_transform['item_category_category_name'],
                'description' => $to_transform['item_category_category_description']
            ],
            'created' => $to_transform['item_category_created_at']
        ];
    }
}
