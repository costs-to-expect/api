<?php
declare(strict_types=1);

namespace App\Models\Transformers;

/**
 * Transform the data returned from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemTransfer extends Transformer
{
    private $data_to_transform;

    public function __construct(array $data_to_transform)
    {
        parent::__construct();

        $this->data_to_transform = $data_to_transform;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->hash->itemPartialTransfer()->encode($this->data_to_transform['id']),
            'from' => [
                'id' => $this->hash->resource()->encode($this->data_to_transform['from_resource_id']),
                'name' => $this->data_to_transform['from_resource_name'],
            ],
            'to' => [
                'id' => $this->hash->resource()->encode($this->data_to_transform['to_resource_id']),
                'name' => $this->data_to_transform['to_resource_name'],
            ],
            'item' => [
                'id' => $this->hash->item()->encode($this->data_to_transform['item_id'])
            ],
            'transferred' => [
                'at' => $this->data_to_transform['created_at'],
                'user' => [
                    'id' => $this->hash->user()->encode($this->data_to_transform['user_id']),
                    'name' => $this->data_to_transform['user_name']
                ]
            ]
        ];
    }
}
