<?php

declare(strict_types=1);

namespace App\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemPartialTransfer extends Transformer
{
    public function format(array $to_transform): void
    {
        $resource_type_id = $this->hash->itemPartialTransfer()->encode($to_transform['id']);
        $from_resource_id = $this->hash->resource()->encode($to_transform['from_resource_id']);
        $to_resource_id = $this->hash->resource()->encode($to_transform['to_resource_id']);
        $item_id = $this->hash->item()->encode($to_transform['item_item_id']);
        $user_id = $this->hash->user()->encode($to_transform['user_id']);

        $this->transformed = [
            'id' => $resource_type_id,
            'from' => [
                'uri' => route('resource.show', ['resource_type_id' => $resource_type_id, 'resource_id' => $from_resource_id], false),
                'id' => $from_resource_id,
                'name' => $to_transform['from_resource_name'],
            ],
            'to' => [
                'uri' => route('resource.show', ['resource_type_id' => $resource_type_id, 'resource_id' => $to_resource_id], false),
                'id' => $to_resource_id,
                'name' => $to_transform['to_resource_name'],
            ],
            'item' => [
                'uri' => route('item.show', ['resource_type_id' => $resource_type_id, 'resource_id' => $from_resource_id, 'item_id' => $item_id], false),
                'id' => $item_id,
                'name' => $to_transform['item_name'],
                'description' => $to_transform['item_description']
            ],
            'percentage' => (int) ($to_transform['percentage']),
            'transferred' => [
                'at' => $to_transform['created_at'],
                'user' => [
                    'uri' => route('permitted-user.show', ['resource_type_id', $resource_type_id, 'permitted_user_id', $user_id], false),
                    'id' => $user_id,
                    'name' => $to_transform['user_name']
                ]
            ]
        ];
    }
}
