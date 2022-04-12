<?php
declare(strict_types=1);

namespace App\Transformers;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
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
            array_key_exists('resource_item_subtype_id', $to_transform) === true &&
            array_key_exists('resource_item_subtype_name', $to_transform) === true &&
            array_key_exists('resource_item_subtype_description', $to_transform) === true
        ) {
            $this->transformed['item_subtype'] = [
                'id' => $this->hash->itemSubtype()->encode($to_transform['resource_item_subtype_id']),
                'name' => $to_transform['resource_item_subtype_name'],
                'description' => $to_transform['resource_item_subtype_description']
            ];
        }
    }
}
