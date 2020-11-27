<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Request\Hash;

/**
 * Our base transformer class, used to convert the results of our queries into
 * a useful structure and then the required format
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
abstract class Transformer
{
    protected Hash $hash;

    protected array $related;

    protected array $transformed;

    /**
     * @param array $to_transform
     * @param array $related Pass in optional related data arrays
     */
    public function __construct(array $to_transform, array $related = [])
    {
        $this->hash = new Hash();

        $this->transformed = [];
        $this->related = $related;

        $this->format($to_transform);
    }

    /**
     * Format the data
     *
     * @param array $to_transform
     */
    abstract protected function format(array $to_transform): void;

    public function asJson(): ?string
    {
        try {
            return json_encode($this->transformed, JSON_THROW_ON_ERROR | 15);
        } catch (\JsonException $e) {
            return null;
        }
    }

    public function asArray(): array
    {
        return $this->transformed;
    }

    protected function assignCategories(array $to_transform): void
    {
        if (
            array_key_exists('category_id', $to_transform) === true &&
            array_key_exists('category_name', $to_transform) === true &&
            array_key_exists('category_description', $to_transform) === true &&
            array_key_exists('item_category_id', $to_transform) === true &&
            $to_transform['category_id'] !== null
        ) {
            $category = [
                'id' => $this->hash->itemCategory()->encode($to_transform['item_category_id']),
                'category_id' => $this->hash->category()->encode($to_transform['category_id']),
                'name' => $to_transform['category_name'],
                'description' => $to_transform['category_description'],
                "subcategories" => []
            ];

            if (
                array_key_exists('subcategory_id', $to_transform) === true &&
                array_key_exists('subcategory_name', $to_transform) === true &&
                array_key_exists('subcategory_description', $to_transform) === true &&
                array_key_exists('item_subcategory_id', $to_transform) === true &&
                $to_transform['subcategory_id'] !== null
            ) {
                $category['subcategories'][] = [
                    'id' => $this->hash->itemSubcategory()->encode($to_transform['item_subcategory_id']),
                    'subcategory_id' => $this->hash->subcategory()->encode($to_transform['subcategory_id']),
                    'name' => $to_transform['subcategory_name'],
                    'description' => $to_transform['subcategory_description']
                ];
            }

            $this->transformed['categories'][] = $category;
        }
    }
}
