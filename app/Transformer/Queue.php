<?php

declare(strict_types=1);

namespace App\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Queue extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->queue()->encode($to_transform['jobs_id']),
            'queue' => $to_transform['jobs_queue'],
            'created' => $to_transform['jobs_created_at']
        ];
    }
}
