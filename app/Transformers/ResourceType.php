<?php

namespace App\Transformers;

use App\Transformers\Resource as ResourceTransformer;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType extends Transformer
{
    private $resource_type;
    private $parameters = [];

    private $resources = [];

    /**
     * ResourceType constructor.
     *
     * @param \App\Models\ResourceType $resource_type
     * @param array $parameters
     */
    public function __construct(\App\Models\ResourceType $resource_type, array $parameters = [])
    {
        parent::__construct();

        $this->resource_type = $resource_type;
        $this->parameters = $parameters;
    }

    /**
     * Format the data
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'id' => $this->hash->encode($this->resource_type->id),
            'name' => $this->resource_type->name,
            'description' => $this->resource_type->description,
            'number_of_resources' => $this->resource_type->numberOfResources(),
            'created' => $this->resource_type->created_at->toDateTimeString()
        ];

        if ($this->parameters['include_resources'] === true) {
            $resourcesCollection = $this->resource_type->resources;

            $resourcesCollection->map(
                function ($resource_item) {
                    $this->resources[] = (new ResourceTransformer($resource_item))->toArray();
                }
            );

            $result['resources'] = $this->resources;
        }

        return $result;
    }
}
