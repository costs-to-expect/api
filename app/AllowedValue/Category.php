<?php
declare(strict_types=1);

namespace App\AllowedValue;

use App\HttpRequest\Hash;

class Category
{
    protected Hash $hash;

    public function __construct()
    {
        $this->hash = new Hash();
    }

    /**
     * Generate the allowed values categories array, will be passed to the
     * Option classes and merged with the fields/parameters
     *
     * @param integer $resource_type_id
     *
     * @return array
     */
    public function allowedValues(int $resource_type_id, string $field_name = 'category_id'): array
    {
        $categories = (new \App\Models\Category())->categoriesByResourceType($resource_type_id);

        $parameters = ['category_id' => ['allowed_values' => []]];

        foreach ($categories as $category) {
            $id = $this->hash->encode('category', $category['category_id']);

            if ($id === false) {
                \App\HttpResponse\Responses::unableToDecode();
            }

            $parameters[$field_name]['allowed_values'][$id] = [
                'value' => $id,
                'name' => $category['category_name'],
                'description' => $category['category_description']
            ];
        }

        return $parameters;
    }
}
