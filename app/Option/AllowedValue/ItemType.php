<?php
declare(strict_types=1);

namespace App\Option\AllowedValue;

use App\Request\Hash;

class ItemType
{
    protected Hash $hash;

    public function __construct()
    {
        $this->hash = new Hash();
    }

    /**
     * Generate the allowed values item type array, will be passed to the
     * Option classes and merged with the fields/parameters
     *
     * @return array
     */
    public function allowedValues(): array
    {
        $parameters = ['item_type_id' => ['allowed_values' => []]];

        $item_types = (new \App\Models\ItemType())->minimisedCollection();

        foreach ($item_types as $item_type) {
            $id = $this->hash->encode('item-type', $item_type['item_type_id']);

            if ($id === false) {
                \App\Response\Responses::unableToDecode();
            }

            $parameters['item_type_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $item_type['item_type_name'],
                'description' => $item_type['item_type_description']
            ];
        }

        return $parameters;
    }
}
