<?php

declare(strict_types=1);

namespace App\Transformer;

use App\Transformer\Resource as ResourceTransformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType extends Transformer
{
    public function format(array $to_transform): void
    {
        $data = null;

        try {
            if (array_key_exists('resource_type_data', $to_transform) && $to_transform['resource_type_data'] !== null) {
                $data = json_decode($to_transform['resource_type_data'], true, 512, JSON_THROW_ON_ERROR);
            }
        } catch (\JsonException $e) {
            $data = [
                'error' => 'Unable to decode data'
            ];
        }

        $resource_type_id = $this->hash->resourceType()->encode($to_transform['resource_type_id']);

        $this->transformed = [
            'id' => $resource_type_id,
            'name' => $to_transform['resource_type_name'],
            'description' => $to_transform['resource_type_description'],
            'data' => $data,
            'created' => $to_transform['resource_type_created_at'],
            'public' => (bool) $to_transform['resource_type_public'],
        ];

        if (
            array_key_exists('resource_type_item_type_id', $to_transform) === true &&
            array_key_exists('resource_type_item_type_name', $to_transform) === true &&
            array_key_exists('resource_type_item_type_description', $to_transform) === true
        ) {
            $id = $this->hash->itemType()->encode($to_transform['resource_type_item_type_id']);
            $this->transformed['item_type'] = [
                'uri' => route('item-type.show', ['item_type_id' => $id], false),
                'id' => $id,
                'name' => $to_transform['resource_type_item_type_name'],
                'friendly_name' => $to_transform['resource_type_item_type_friendly_name'],
                'description' => $to_transform['resource_type_item_type_description']
            ];
        }

        if (array_key_exists('resource_type_resources', $to_transform)) {
            $this->transformed['resources']['uri'] = route('resource.list', ['resource_type_id' => $resource_type_id], false);
            $this->transformed['resources']['count'] = $to_transform['resource_type_resources'];
        }

        if (array_key_exists('resources', $this->related) === true) {
            foreach ($this->related['resources'] as $resource) {
                $this->transformed['resources']['collection'][] = (new ResourceTransformer($resource))->asArray();
            }
        }

        if (array_key_exists('permitted_users', $this->related) === true) {
            $this->transformed['permitted_users']['uri'] = route('permitted-user.list', ['resource_type_id' => $resource_type_id], false);
            $this->transformed['permitted_users']['count'] = count($this->related['permitted_users']);
            foreach ($this->related['permitted_users'] as $permitted_user) {
                $this->transformed['permitted_users']['collection'][] = (new PermittedUser($permitted_user))->asArray();
            }
        }
    }
}
