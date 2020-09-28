<?php
declare(strict_types=1);

namespace App\Models\Transformers\Item\Summary;

use App\Models\Transformers\Transformer;

/**
 * Transform the data from our queries into the format we want to display
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ExpenseItem extends Transformer
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
