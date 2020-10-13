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
class GameItemByResource extends Transformer
{
    public function format(array $to_transform): void
    {
        foreach ($to_transform as $summary) {
            $this->transformed[] = [
                'id' => $this->hash->resource()->encode($summary['id']),
                'name' => $summary['name'],
                'count' => (int) $summary['count']
            ];
        }
    }
}
