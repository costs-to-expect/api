<?php

declare(strict_types=1);

namespace App\ItemType\AllocatedExpense\Transformer;

use App\Transformer\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryByCategory extends Transformer
{
    public function format(array $to_transform): void
    {
        $temporary = [];

        foreach ($to_transform as $summary) {
            if (array_key_exists($summary['id'], $temporary) === false) {
                $temporary[$summary['id']] = [
                    'id' => $this->hash->category()->encode($summary['id']),
                    'name' => $summary['name'],
                    'description' => $summary['description'],
                    'subtotals' => []
                ];
            }

            $temporary[$summary['id']]['subtotals'][] = [
                'currency' => [
                    'code' => $summary['currency_code'],
                ],
                'count' => $summary['total_count'],
                'subtotal' => number_format((float)$summary['total'], 2, '.', '')
            ];
        }

        foreach ($temporary as $temp) {
            $this->transformed[] = $temp;
        }
    }
}
