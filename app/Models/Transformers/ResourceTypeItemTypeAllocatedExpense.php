<?php
declare(strict_types=1);

namespace App\Models\Transformers;

/**
 * Transform the data array into the format we require for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItemTypeAllocatedExpense extends Transformer
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
            'total' => number_format((float) $this->item['item_total'], 2, '.', ''),
            'percentage' => (int) $this->item['item_percentage'],
            'actualised_total' => number_format((float) $this->item['item_actualised_total'], 2, '.', ''),
            'effective_date' => $this->item['item_effective_date'],
            'created' => $this->item['item_created_at'],
            'resource' => [
                'id' => $this->hash->resource()->encode($this->item['resource_id']),
                'name' => $this->item['resource_name'],
                'description' => $this->item['resource_description']
            ]
        ];

        if (
            array_key_exists('category_id', $this->item) === true &&
            array_key_exists('category_name', $this->item) === true
        ) {
            $item['category'] = [
                'id' => $this->hash->category()->encode($this->item['category_id']),
                'name' => $this->item['category_name'],
                'description' => $this->item['category_description']
            ];

            if (
                array_key_exists('subcategory_id', $this->item) === true &&
                array_key_exists('subcategory_name', $this->item) === true
            ) {
                $item['subcategory'] = [
                    'id' => $this->hash->subCategory()->encode($this->item['subcategory_id']),
                    'name' => $this->item['subcategory_name'],
                    'description' => $this->item['subcategory_description']
                ];
            }
        }

        return $item;
    }
}
