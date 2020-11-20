<?php
declare(strict_types=1);

namespace App\ItemType\SimpleItem;

use App\Models\Transformers\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryTransformerByResource extends Transformer
{
    public function format(array $to_transform): void
    {
        foreach ($to_transform as $summary) {
            $this->transformed[] = [
                'id' => $this->hash->resource()->encode($summary['id']),
                'name' => $summary['name'],
                'count' => (int) $summary['total_count'],
                'total' => (int) $summary['total']
            ];
        }
    }
}
