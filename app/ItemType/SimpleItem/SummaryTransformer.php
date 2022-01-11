<?php
declare(strict_types=1);

namespace App\ItemType\SimpleItem;

use App\Transformers\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryTransformer extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'count' => (int) $to_transform['total_count'],
            'total' => (int) $to_transform['total']
        ];
    }
}
