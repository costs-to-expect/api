<?php
declare(strict_types=1);

namespace App\Models\Transformers\ItemType;

use App\Models\Transformers\Transformer;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class AllocatedExpense extends Transformer
{
    protected $item;

    public function __construct(array $item)
    {
        parent::__construct();

        $this->item = $item;
    }

    public function toArray(): array
    {
        $item = [
            'id' => $this->hash->item()->encode($this->item['item_id']),
            'name' => $this->item['item_name'],
            'description' => $this->item['item_description'],
            'total' => number_format((float) $this->item['item_total'],2, '.', ''),
            'percentage' => $this->item['item_percentage'],
            'actualised_total' => number_format((float) $this->item['item_actualised_total'], 2, '.', ''),
            'effective_date' => $this->item['item_effective_date'],
            'created' => $this->item['item_created_at'],
            'updated' => $this->item['item_updated_at']
        ];

        if (
            array_key_exists('category_id', $this->item) === true &&
            array_key_exists('category_name', $this->item) === true
        ) {
            if ($this->item['category_id'] !== null) {
                $item['category'] = [
                    'id' => $this->hash->category()->encode($this->item['category_id']),
                    'name' => $this->item['category_name'],
                    'description' => $this->item['category_description'],
                    'uri' => '/category/' . $this->hash->itemCategory()->encode($this->item['item_category_id'])
                ];
            } else {
                $item['category'] = null;    
            }

            if (
                array_key_exists('subcategory_id', $this->item) === true &&
                array_key_exists('subcategory_name', $this->item) === true
            ) {
                if ($this->item['subcategory_id'] !== null) {
                    $item['subcategory'] = [
                        'id' => $this->hash->subCategory()->encode($this->item['subcategory_id']),
                        'name' => $this->item['subcategory_name'],
                        'description' => $this->item['subcategory_description'],
                        'uri' => '/category/' .
                            $this->hash->itemCategory()->encode($this->item['item_category_id']) .
                            '/subcategory/' . $this->hash->itemSubCategory()->encode($this->item['item_subcategory_id'])
                    ];
                } else {
                    $item['subcategory'] = null;
                }
            }
        }

        return $item;
    }
}
