<?php
declare(strict_types=1);

namespace App\Models\Transformers;

/**
 * Transform the data from our queries into the format we want to display
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestLog extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'method' => $to_transform['method'],
            'source' => $to_transform['source'],
            'request_uri' => $to_transform['request'],
            'created' => $to_transform['created_at']
        ];
    }
}
