<?php

declare(strict_types=1);

namespace App\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemData extends Transformer
{
    public function format(array $to_transform): void
    {
        $data = null;

        try {
            if (
                array_key_exists('item_data_json', $to_transform) &&
                $to_transform['item_data_json'] !== null
            ) {
                $data = json_decode(
                    $to_transform['item_data_json'],
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
            }
        } catch (\JsonException $e) {
            $data = [
                'error' => 'Unable to decode data'
            ];
        }

        $this->transformed = [
            'key' => $to_transform['item_data_key'],
            'value' => $data,
            'created' => $to_transform['item_data_created_at'],
            'updated' => $to_transform['item_data_updated_at'],
        ];
    }
}
