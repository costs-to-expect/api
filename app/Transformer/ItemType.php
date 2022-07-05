<?php

declare(strict_types=1);

namespace App\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemType extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->itemType()->encode($to_transform['item_type_id']),
            'name' => $to_transform['item_type_name'],
            'friendly_name' => $to_transform['item_type_friendly_name'],
            'description' => $to_transform['item_type_description'],
            'example' => $to_transform['item_type_example'],
            'created' => $to_transform['item_type_created_at']
        ];
    }
}
