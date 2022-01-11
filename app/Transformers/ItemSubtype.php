<?php
declare(strict_types=1);

namespace App\Transformers;

/**
 * Transform the data from our queries into the format we want to display
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubtype extends Transformer
{
    protected function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->itemSubtype()->encode($to_transform['item_subtype_id']),
            'name' => $to_transform['item_subtype_name'],
            'friendly_name' => $to_transform['item_subtype_friendly_name'],
            'description' => $to_transform['item_subtype_description'],
            'created' => $to_transform['item_subtype_created_at']
        ];
    }
}
