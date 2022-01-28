<?php
declare(strict_types=1);

namespace App\Transformers;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Subcategory extends Transformer
{
    protected function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->subcategory()->encode($to_transform['subcategory_id']),
            'name' => $to_transform['subcategory_name'],
            'description' => $to_transform['subcategory_description'],
            'created' => $to_transform['subcategory_created_at']
        ];
    }
}
