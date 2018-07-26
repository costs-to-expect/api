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
    protected $resource_type;

    protected $resources = [];

    public function __construct(\App\Models\ResourceType $resource_type)
    {
        parent::__construct();

        $this->resource_type = $resource_type;
    }

    public function toArray(): array
    {
        $resourcesCollection = $this->resource_type->resources;

        $resourcesCollection->map(
            function($resource_item) {
                $this->resources[] = (new ResourceTransformer($resource_item))->toArray();
            }
        );

        return [
            'id' => $this->hash->encode($this->resource_type->id),
            'name' => $this->resource_type->name,
            'description' => $this->resource_type->description,
            'created' => $this->resource_type->created_at->toDateTimeString(),
            'resources' => $this->resources
        ];
    }
}
