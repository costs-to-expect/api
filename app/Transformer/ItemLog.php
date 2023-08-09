<?php

declare(strict_types=1);

namespace App\Transformer;

use JsonException;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemLog extends Transformer
{
    public function format(array $to_transform): void
    {
        $parameters = null;

        try {
            if (
                array_key_exists('item_log_parameters', $to_transform) &&
                $to_transform['item_log_parameters'] !== null
            ) {
                $parameters = json_decode(
                    $to_transform['item_log_parameters'],
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
            }
        } catch (JsonException $e) {
            $parameters = [
                'error' => 'Unable to decode data'
            ];
        }

        $this->transformed = [
            'id' => $this->hash->itemLog()->encode($to_transform['item_log_id']),
            'message' => $to_transform['item_log_message'],
            'parameters' => $parameters,
            'created' => $to_transform['item_log_created_at'],
            'updated' => $to_transform['item_log_updated_at'],
        ];
    }
}
