<?php
declare(strict_types=1);

namespace App\AllowedValue;

use App\HttpRequest\Hash;

class Subcategory
{
    protected Hash $hash;

    public function __construct()
    {
        $this->hash = new Hash();
    }

    /**
     * Generate the allowed values subcategories array, will be passed to the
     * Option classes and merged with the fields/parameters
     *
     * @param integer $category_id
     *
     * @return array
     */
    public function allowedValues(int $category_id): array
    {
        $subcategories = (new \App\Models\Subcategory())
            ->select('id', 'name', 'description')
            ->where('category_id', '=', $category_id)
            ->get();

        $parameters = ['subcategory_id' => ['allowed_values' => []]];

        foreach ($subcategories as $subcategory) {
            $id = $this->hash->encode('subcategory', $subcategory->id);

            if ($id === false) {
                \App\HttpResponse\Responses::unableToDecode();
            }

            $parameters['subcategory_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $subcategory->name,
                'description' => $subcategory->description
            ];
        }

        return $parameters;
    }
}
