<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Models\ResourceType as ResourceTypeModel;
use App\Transformers\Resource as ResourceTransformer;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
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
     * @param ResourceTypeModel $resource_type
     * @param array $parameters
     */
    public function __construct(ResourceTypeModel $resource_type, array $parameters = [])
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
            'id' => $this->hash->resourceType()->encode($this->resource_type->id),
            'name' => $this->resource_type->name,
            'description' => $this->resource_type->description,
            'created' => $this->resource_type->created_at->toDateTimeString(),
            'public' => !boolval($this->resource_type->private),
            'resources_count' => $this->resource_type->resources_count()
        ];

        if (isset($this->parameters['include_resources']) && $this->parameters['include_resources'] === true) {
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
