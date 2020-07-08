<?php
declare(strict_types=1);

namespace App\Option\Value;

use App\Request\Hash;

class Resource
{
    private Hash $hash;

    public function __construct()
    {
        $this->hash = new Hash();
    }

    /**
     * Generate the allowed values resources array, will be passed to the
     * Option classes and merged with the fields/parameters
     *
     * @param integer $resource_type_id
     * @param integer $exclude_resource_id
     *
     * @return array
     */
    public function allowedValues(int $resource_type_id, int $exclude_resource_id): array
    {
        $resources = (new \App\Models\Resource())->resourcesForResourceType(
            $resource_type_id,
            $exclude_resource_id
        );

        $parameters = ['resource_id' => []];
        foreach ($resources as $resource) {
            $id = $this->hash->encode('resource', $resource['resource_id']);

            if ($id === false) {
                \App\Response\Responses::unableToDecode();
            }

            $parameters['resource_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $resource['resource_name'],
                'description' => $resource['resource_description']
            ];
        }

        return $parameters;
    }
}
