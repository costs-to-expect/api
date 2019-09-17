<?php
declare(strict_types=1);

namespace App\Models\Transformers;

use App\Models\Transformers\Resource as ResourceTransformer;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType extends Transformer
{
    private $data_to_transform;

    private $resources = [];

    /**
     * ResourceType constructor.
     *
     * @param array $data_to_transform
     * @param array $resources
     */
    public function __construct(array $data_to_transform, array $resources = [])
    {
        parent::__construct();

        $this->data_to_transform = $data_to_transform;
        $this->resources = $resources;
    }

    /**
     * Format the data
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'id' => $this->hash->resourceType()->encode($this->data_to_transform['resource_type_id']),
            'name' => $this->data_to_transform['resource_type_name'],
            'description' => $this->data_to_transform['resource_type_description'],
            'created' => $this->data_to_transform['resource_type_created_at'],
            'public' => boolval($this->data_to_transform['resource_type_public']),
        ];

        if (array_key_exists('resource_type_resources', $this->data_to_transform)) {
            $result['resources']['count'] = $this->data_to_transform['resource_type_resources'];
        }

        foreach ($this->resources as $resource) {
            $result['resources']['collection'][] = (new ResourceTransformer($resource))->toArray();
        }

        return $result;
    }
}
