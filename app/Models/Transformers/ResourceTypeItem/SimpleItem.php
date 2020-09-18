<?php
declare(strict_types=1);

namespace App\Models\Transformers\ResourceTypeItem;

use App\Models\Transformers\Transformer;

/**
 * Transform the data from our queries into the format we want to display
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SimpleItem extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->item()->encode($to_transform['item_id']),
            'name' => $to_transform['item_name'],
            'description' => $to_transform['item_description'],
            'quantity' => (int) $to_transform['item_quantity'],
            'created' => $to_transform['item_created_at'],
            'updated' => $to_transform['item_updated_at'],
            'resource' => [
                'id' => $this->hash->resource()->encode($to_transform['resource_id']),
                'name' => $to_transform['resource_name'],
                'description' => $to_transform['resource_description']
            ]
        ];
    }
}
