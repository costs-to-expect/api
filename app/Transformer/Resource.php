<?php

declare(strict_types=1);

namespace App\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Resource extends Transformer
{
    public function format(array $to_transform): void
    {
        $data = null;

        try {
            if (array_key_exists('resource_data', $to_transform) && $to_transform['resource_data'] !== null) {
                $data = json_decode($to_transform['resource_data'], true, 512, JSON_THROW_ON_ERROR);
            }
        } catch (\JsonException $e) {
            $data = [
                'error' => 'Unable to decode data'
            ];
        }

        $this->transformed = [
            'id' => $this->hash->resource()->encode($to_transform['resource_id']),
            'name' => $to_transform['resource_name'],
            'description' => $to_transform['resource_description'],
            'data' => $data,
            'created' => $to_transform['resource_created_at']
        ];

        if (
            array_key_exists('resource_type_item_type_id', $to_transform) === true &&
            array_key_exists('resource_item_subtype_id', $to_transform) === true &&
            array_key_exists('resource_item_subtype_name', $to_transform) === true &&
            array_key_exists('resource_item_subtype_description', $to_transform) === true
        ) {
            $item_type_id = $this->hash->itemType()->encode($to_transform['resource_type_item_type_id']);
            $item_sub_type_id = $this->hash->itemSubtype()->encode($to_transform['resource_item_subtype_id']);

            $this->transformed['item_subtype'] = [
                'uri' => route('item-subtype.show', ['item_type_id' => $item_type_id, 'item_subtype_id' => $item_sub_type_id], false),
                'id' => $item_sub_type_id,
                'name' => $to_transform['resource_item_subtype_name'],
                'description' => $to_transform['resource_item_subtype_description']
            ];
        }
    }
}
