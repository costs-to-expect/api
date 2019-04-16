<?php
declare(strict_types=1);

namespace App\Models\Transformers;

/**
 * Transform the data array into the format we require for the API
 *
 * This is an updated version of the transformers, the other transformers need to
 * be updated to operate on array rather than collections
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItem extends Transformer
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
        return [
            'id' => $this->hash->item()->encode($this->item['item_id']),
            'description' => $this->item['item_description'],
            'total' => number_format((float) $this->item['item_total'], 2),
            'percentage' => (int) $this->item['item_percentage'],
            'actualised_total' => number_format((float) $this->item['item_actualised_total'], 2),
            'effective_date' => $this->item['item_effective_date'],
            'created' => $this->item['item_created_at'],
            'resource' => [
                'id' => $this->hash->resource()->encode($this->item['resource_id']),
                'name' => $this->item['resource_name']
            ]
        ];
    }
}
