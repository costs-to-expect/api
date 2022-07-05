<?php

declare(strict_types=1);

namespace App\ItemType\AllocatedExpense\Transformer;

use App\Transformer\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItem extends Transformer
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
            'total' => number_format((float) $to_transform['item_total'], 2, '.', ''),
            'percentage' => (int) $to_transform['item_percentage'],
            'actualised_total' => number_format((float) $to_transform['item_actualised_total'], 2, '.', ''),
            'effective_date' => $to_transform['item_effective_date'],
            'created' => $to_transform['item_created_at'],
            'updated' => $to_transform['item_updated_at'],
            'categories' => [],
            'resource' => [
                'id' => $this->hash->resource()->encode($to_transform['resource_id']),
                'name' => $to_transform['resource_name'],
                'description' => $to_transform['resource_description']
            ]
        ];

        $this->assignCategories($to_transform);
    }
}
