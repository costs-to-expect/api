<?php
declare(strict_types=1);

namespace App\ItemType\AllocatedExpense;

use App\Models\Transformers\Transformer as BaseTransformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Transformer extends BaseTransformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->item()->encode($to_transform['item_id']),
            'name' => $to_transform['item_name'],
            'description' => $to_transform['item_description'],
            'currency' => [
                'id' => $this->hash->currency()->encode($to_transform['item_currency_id']),
                'code' => $to_transform['item_currency_code'],
                'name' => $to_transform['item_currency_name'],
            ],
            'total' => number_format((float) $to_transform['item_total'],2, '.', ''),
            'percentage' => $to_transform['item_percentage'],
            'actualised_total' => number_format((float) $to_transform['item_actualised_total'], 2, '.', ''),
            'effective_date' => $to_transform['item_effective_date'],
            'categories' => [],
            'created' => $to_transform['item_created_at'],
            'updated' => $to_transform['item_updated_at']
        ];

        $this->assignCategories($to_transform);
    }
}
