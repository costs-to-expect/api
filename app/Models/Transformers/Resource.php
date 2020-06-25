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
class Resource extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->resource()->encode($to_transform['resource_id']),
            'name' => $to_transform['resource_name'],
            'description' => $to_transform['resource_description'],
            'effective_date' => $to_transform['resource_effective_date'],
            'created' => $to_transform['resource_created_at']
        ];
    }
}
