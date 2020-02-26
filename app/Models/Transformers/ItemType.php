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
class ItemType extends Transformer
{
    private $data_to_transform;

    /**
     * @param array $data_to_transform
     */
    public function __construct(array $data_to_transform)
    {
        parent::__construct();

        $this->data_to_transform = $data_to_transform;
    }

    /**
     * Format the data
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->hash->itemType()->encode($this->data_to_transform['item_type_id']),
            'name' => $this->data_to_transform['item_type_name'],
            'description' => $this->data_to_transform['item_type_description'],
            'created' => $this->data_to_transform['item_type_created_at']
        ];
    }
}
