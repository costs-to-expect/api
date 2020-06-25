<?php
declare(strict_types=1);

namespace App\Models\Transformers\Summary;

use App\Models\Transformers\Transformer;

/**
 * Transform the data from our queries into the format we want to display
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategory extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->category()->encode($to_transform['id']),
            'name' => $to_transform['name'],
            'description' => $to_transform['description'],
            'total' => number_format((float) $to_transform['total'], 2, '.', '')
        ];
    }
}
