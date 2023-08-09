<?php

declare(strict_types=1);

namespace App\ItemType\Game\Transformer;

use App\Transformer\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Summary extends Transformer
{
    public function format(array $to_transform): void
    {
        if (
            array_key_exists('resource_type_id', $to_transform) &&
            array_key_exists('resource_type_name', $to_transform) &&
            array_key_exists('resource_type_description', $to_transform)
        ) {
            $this->transformed['resource_type'] = [
                'id' => $this->hash->resourceType()->encode($to_transform['resource_type_id']),
                'name' => $to_transform['resource_type_name'],
                'description' => $to_transform['resource_type_description']
            ];
        }

        if (
            array_key_exists('resource_id', $to_transform) &&
            array_key_exists('resource_name', $to_transform) &&
            array_key_exists('resource_description', $to_transform)
        ) {
            $this->transformed['resource'] = [
                'id' => $this->hash->resource()->encode($to_transform['resource_id']),
                'name' => $to_transform['resource_name'],
                'description' => $to_transform['resource_description']
            ];

            if (
                array_key_exists('resource_item_subtype_id', $to_transform) &&
                array_key_exists('resource_item_subtype_name', $to_transform) &&
                array_key_exists('resource_item_subtype_description', $to_transform)
            ) {
                $this->transformed['resource']['item_subtype'] = [
                    'id' => $this->hash->itemSubtype()->encode($to_transform['resource_item_subtype_id']),
                    'name' => $to_transform['resource_item_subtype_name'],
                    'description' => $to_transform['resource_item_subtype_description']
                ];
            }
        }

        $this->transformed['count'] = (int) $to_transform['count'];
    }
}
