<?php
declare(strict_types=1);

namespace App\Models\Transformers;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategory extends Transformer
{
    protected $data_to_transform;

    public function __construct(array $data_to_transform)
    {
        parent::__construct();

        $this->data_to_transform = $data_to_transform;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->hash->itemCategory()->encode($this->data_to_transform['item_category_id']),
            'category' => [
                'name' => $this->data_to_transform['item_category_category_name'],
                'description' => $this->data_to_transform['item_category_category_description']
            ],
            'created' => $this->data_to_transform['item_category_created_at']
        ];
    }
}
