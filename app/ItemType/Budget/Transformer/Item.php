<?php

declare(strict_types=1);

namespace App\ItemType\Budget\Transformer;

use App\Transformer\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Item extends Transformer
{
    public function format(array $to_transform): void
    {
        $frequency = [];

        $budget_item_id = $this->hash->item()->encode($to_transform['item_id']);

        try {
            if (array_key_exists('item_frequency', $to_transform)) {
                $frequency = json_decode($to_transform['item_frequency'], true, 512, JSON_THROW_ON_ERROR);
            }
        } catch (\JsonException $e) {
            $frequency = [
                'error' => 'Unable to decode the frequency data'
            ];
        }

        $currency = null;
        if (
           array_key_exists('item_currency_id', $to_transform) &&
           array_key_exists('item_currency_code', $to_transform) &&
           array_key_exists('item_currency_name', $to_transform) &&
           $to_transform['item_currency_id'] !== null
       ) {
            $currency_id = $this->hash->currency()->encode($to_transform['item_currency_id']);

            $currency = [
                'id' => $currency_id,
                'name' => $to_transform['item_currency_name'],
                'code' => $to_transform['item_currency_code'],
                'uri' => route('currency.show', ['currency_id' => $currency_id], false)
            ];
        }

        $this->transformed = [
            'id' => $budget_item_id,
            'name' => $to_transform['item_name'],
            'account' => $to_transform['item_account'],
            'target_account' => $to_transform['item_target_account'],
            'description' => $to_transform['item_description'],
            'amount' => number_format((float) $to_transform['item_amount'], 2, '.', ''),
            'currency' => $currency,
            'category' => $to_transform['item_category'],
            'start_date' => $to_transform['item_start_date'],
            'end_date' => $to_transform['item_end_date'],
            'disabled' => (bool) $to_transform['item_disabled'],
            'frequency' => $frequency,
            'created' => $to_transform['item_created_at'],
            'updated' => $to_transform['item_updated_at']
        ];
    }
}
