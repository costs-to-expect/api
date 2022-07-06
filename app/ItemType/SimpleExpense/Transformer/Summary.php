<?php

declare(strict_types=1);

namespace App\ItemType\SimpleExpense\Transformer;

use App\Transformer\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Summary extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'currency' => [
                'code' => $to_transform['currency_code']
            ],
            'count' => $to_transform['total_count'],
            'subtotal' => number_format((float) $to_transform['total'], 2, '.', '')
        ];
    }
}
