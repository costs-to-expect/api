<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Transformers\Transformer;

/**
 * Transform the data from our queries into the format we want to display
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Currency extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->currency()->encode($to_transform['currency_id']),
            'code' => $to_transform['currency_code'],
            'name' => $to_transform['currency_name'],
            'created' => $to_transform['currency_created_at']
        ];
    }
}
