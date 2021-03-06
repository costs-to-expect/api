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
class ItemPartialTransfer extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->itemPartialTransfer()->encode($to_transform['id']),
            'from' => [
                'id' => $this->hash->resource()->encode($to_transform['from_resource_id']),
                'name' => $to_transform['from_resource_name'],
            ],
            'to' => [
                'id' => $this->hash->resource()->encode($to_transform['to_resource_id']),
                'name' => $to_transform['to_resource_name'],
            ],
            'item' => [
                'id' => $this->hash->item()->encode($to_transform['item_item_id']),
                'name' => $to_transform['item_name'],
                'description' => $to_transform['item_description']
            ],
            'percentage' => (int) ($to_transform['percentage']),
            'transferred' => [
                'at' => $to_transform['created_at'],
                'user' => [
                    'id' => $this->hash->user()->encode($to_transform['user_id']),
                    'name' => $to_transform['user_name']
                ]
            ]
        ];
    }
}
