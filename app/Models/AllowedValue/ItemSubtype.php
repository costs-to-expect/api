<?php
declare(strict_types=1);

namespace App\Models\AllowedValue;

use App\HttpRequest\Hash;

class ItemSubtype
{
    protected Hash $hash;

    public function __construct()
    {
        $this->hash = new Hash();
    }

    public function allowedValues(int $item_type_id): array
    {
        $parameters = ['item_subtype_id' => ['allowed_values' => []]];

        $item_subtypes = (new \App\Models\ItemSubtype())->minimisedCollection($item_type_id);

        foreach ($item_subtypes as $item_subtype) {
            $id = $this->hash->encode('item-subtype', $item_subtype['item_subtype_id']);

            if ($id === false) {
                \App\HttpResponse\Response::unableToDecode();
            }

            $parameters['item_subtype_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $item_subtype['item_subtype_name'],
                'description' => $item_subtype['item_subtype_description']
            ];
        }

        return $parameters;
    }
}
