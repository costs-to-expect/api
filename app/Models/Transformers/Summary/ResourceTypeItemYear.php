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
class ResourceTypeItemYear extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $to_transform['year'],
            'year' => $to_transform['year'],
            'total' => (float) $to_transform['total']
        ];
    }
}
