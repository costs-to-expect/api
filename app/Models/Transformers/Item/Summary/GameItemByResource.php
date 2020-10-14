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

            $transformed['resource'] = [
                'id' => $this->hash->resource()->encode($summary['resource_id']),
                'name' => $summary['resource_name'],
                'description' => $summary['resource_description']
            ];

            if (
                array_key_exists('resource_item_subtype_id', $summary) === true &&
                array_key_exists('resource_item_subtype_name', $summary) === true &&
                array_key_exists('resource_item_subtype_description', $summary) === true
            ) {
                $transformed['item_subtype'] = [
                    'id' => $this->hash->itemSubtype()->encode($summary['resource_item_subtype_id']),
                    'name' => $summary['resource_item_subtype_name'],
                    'description' => $summary['resource_item_subtype_description']
                ];
            }

            $transformed['count'] = (int) $summary['count'];

            $this->transformed[] = $transformed;
        }
    }
}
