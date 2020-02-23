<?php
declare(strict_types=1);

namespace App\Models\Transformers\ResourceTypeItemType;

use App\Models\Transformers\Transformer;

/**
 * Transform the data array into the format we require for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SimpleItem extends Transformer
{
    protected $item;

    /**
     * @param array $item
     */
    public function __construct(array $item)
    {
        parent::__construct();

        $this->item = $item;
    }

    /**
     * Return the formatted array
     *
     * @return array
     */
    public function toArray(): array
    {
        $item = [
            'id' => $this->hash->item()->encode($this->item['item_id']),
            'name' => $this->item['item_name'],
            'description' => $this->item['item_description'],
            'quantity' => (int) $this->item['item_quantity'],
            'created' => $this->item['item_created_at'],
            'resource' => [
                'id' => $this->hash->resource()->encode($this->item['resource_id']),
                'name' => $this->item['resource_name'],
                'description' => $this->item['resource_description']
            ]
        ];

        return $item;
    }
}
