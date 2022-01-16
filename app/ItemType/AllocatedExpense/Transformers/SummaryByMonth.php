<?php
declare(strict_types=1);

namespace App\ItemType\AllocatedExpense\Transformers;

use App\Transformers\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryByMonth extends Transformer
{
    public function format(array $to_transform): void
    {
        $temporary = [];

        foreach ($to_transform as $summary) {
            if (array_key_exists($summary['month'], $temporary) === false) {
                $temporary[$summary['month']] = [
                    'id' => $summary['month'],
                    'month' => date("F", mktime(0, 0, 0, $summary['month'], 1)),
                    'subtotals' => []
                ];
            }

            $temporary[$summary['month']]['subtotals'][] = [
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
