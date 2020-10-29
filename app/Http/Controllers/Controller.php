<?php

namespace App\Http\Controllers;

use App\Models\ResourceTypeAccess;
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
    }

    protected function setPermittedResourceTypes(): void
    {
        if (auth('api')->user() !== null && auth()->guard('api')->check() === true) {
            $this->user_id = auth('api')->user()->id; // Safe as check above ensures not null

            $cache_control = new Control($this->user_id, true);
            $cache_control->setTtlOneHour();

            $cache_collection = new Collection();
            $cache_collection->setFromCache($cache_control->get('/v2/permitted-resource-types'));

            if ($cache_control->cacheable() === false || $cache_collection->valid() === false) {

                $permitted_resource_types = (new ResourceTypeAccess())->permittedResourceTypes($this->user_id);

                $cache_collection->create(
                    count($permitted_resource_types),
                    $permitted_resource_types,
                    [],
                    []
                );
                $cache_control->put('/v2/permitted-resource-types', $cache_collection->content());
            }

            $this->permitted_resource_types = $cache_collection->collection();
        }
    }

    protected function excludePublicResourceTypes(): void
    {
        if (Boolean::convertedValue(request()->query('exclude-public')) === true) {
            $this->include_public = false;
        }
    }
}
