<?php

namespace App\Http\Controllers;

use App\Models\ResourceType;
use App\Models\ResourceAccess;
use App\Request\Hash;
use App\Request\Validate\Boolean;
use App\Response\Cache\Collection;
use App\Response\Cache\Control;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected Hash $hash;

    protected bool $include_public = true;

    protected array $permitted_resource_types = [];

    protected array $viewable_resource_types = [];

    /**
     * @var integer|null
     */
    protected ?int $user_id = null;

    /**
     * @var bool Allow the entire collection to be returned ignoring pagination
     */
    protected bool $allow_entire_collection = false;

    public function __construct()
    {
        $this->hash = new Hash();

        $this->excludePublicResourceTypes();

        $this->setPermittedResourceTypes();

        $this->setViewableResourceTypes();
    }

    protected function excludePublicResourceTypes(): void
    {
        if (Boolean::convertedValue(request()->query('exclude-public')) === true) {
            $this->include_public = false;
        }
    }

    protected function setPermittedResourceTypes(): void
    {
        if (
            auth('api')->user() !== null &&
            auth()->guard('api')->check() === true
        ) {
            $this->user_id = auth('api')->user()->id; // Safe as check above ensures not null

            $cache_control = new Control(true, $this->user_id);
            $cache_control->setTtlOneWeek();

            $cache_collection = new Collection();
            $cache_collection->setFromCache($cache_control->getByKey('/v2/permitted-resource-types'));

            if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {

                $permitted_resource_types = (new ResourceAccess())->permittedResourceTypes($this->user_id);

                $cache_collection->create(
                    count($permitted_resource_types),
                    $permitted_resource_types,
                    [],
                    []
                );
                $cache_control->putByKey('/v2/permitted-resource-types', $cache_collection->content());
            }

            $this->permitted_resource_types = $cache_collection->collection();
        }
    }

    protected function setViewableResourceTypes(): void
    {
        $cache_control = new Control();
        $cache_control->setTtlOneMonth();

        if (
            auth('api')->user() !== null &&
            auth()->guard('api')->check() === true
        ) {
            $cache_control = new Control(true, $this->user_id);
            $cache_control->setTtlOneWeek();
        }

        $uri = '/v2/viewable-resource-types?exclude-public=' . ($this->include_public === true ? 'false' : 'true');

        $cache_collection = new Collection();
        $cache_collection->setFromCache($cache_control->getByKey($uri));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {

            $viewable_resource_types = array_merge(
                ($this->include_public === true ? (new ResourceType())->publicResourceTypes() : []),
                $this->permitted_resource_types
            );

            $cache_collection->create(
                count($viewable_resource_types),
                $viewable_resource_types,
                [],
                []
            );

            $cache_control->putByKey($uri, $cache_collection->content());
        }

        $this->viewable_resource_types = $cache_collection->collection();
    }

    protected function writeAccessToResourceType(int $resource_type_id): bool
    {
        return in_array($resource_type_id, $this->permitted_resource_types, true) === true;
    }

    protected function viewAccessToResourceType(int $resource_type_id): bool
    {
        return in_array($resource_type_id, $this->viewable_resource_types, true) === true;
    }

    protected function permissions(int $resource_type_id): array
    {
        return [
            'view' => $this->viewAccessToResourceType($resource_type_id),
            'manage' => $this->writeAccessToResourceType($resource_type_id)
        ];
    }
}
