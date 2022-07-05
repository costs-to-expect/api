<?php
declare(strict_types=1);

namespace App\Models\AllowedValue;

use App\HttpRequest\Hash;

class ItemType
{
    protected Hash $hash;

    public function __construct()
    {
        $this->hash = new Hash();
    }

    public function allowedValues(string $field = 'item_type_id'): array
    {
        $parameters = [$field => ['allowed_values' => []]];

        $item_types = (new \App\Models\ItemType())->minimisedCollection();

        foreach ($item_types as $item_type) {
            $id = $this->hash->encode('item-type', $item_type['item_type_id']);

            if ($id === false) {
                \App\HttpResponse\Response::unableToDecode();
            }

            $parameters[$field]['allowed_values'][$id] = [
                'value' => $id,
                'name' => $item_type['item_type_name'],
                'description' => $item_type['item_type_description']
            ];
        }

        return $parameters;
    }
}
