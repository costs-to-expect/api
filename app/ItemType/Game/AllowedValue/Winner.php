<?php
declare(strict_types=1);

namespace App\ItemType\Game\AllowedValue;

use App\Request\Hash;

class Winner
{
    protected Hash $hash;

    public function __construct()
    {
        $this->hash = new Hash();
    }

    public function allowedValues(int $resource_type_id): array
    {
        $categories = (new \App\Models\Category())->categoriesByResourceType($resource_type_id);

        $parameters = ['winner_id' => ['allowed_values' => []]];

        foreach ($categories as $category) {
            $id = $this->hash->encode('category', $category['category_id']);

            if ($id === false) {
                \App\HttpResponse\Responses::unableToDecode();
            }

            $parameters['winner_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $category['category_name'],
                'description' => $category['category_description']
            ];
        }

        return $parameters;
    }
}
