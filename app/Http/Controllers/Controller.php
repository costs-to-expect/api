<?php

namespace App\Http\Controllers;

use App\Models\ResourceType;
use App\Models\ResourceTypeAccess;
use App\Request\Hash;
use App\Request\Validate\Boolean;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected Hash $hash;

    protected bool $include_public;

    protected array $permitted_resource_types = [];

    protected array $public_resource_types = [];

    protected ResourceTypeAccess $resource_type_access;

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

        $this->resource_type_access = new ResourceTypeAccess();

        $this->middleware(function ($request, $next) {
            $this->setGlobalPropertyValues();

            return $next($request);
        });
    }

    /**
     * Set the values for the controller properties, used by every controller
     *
     * @return void
     */
    protected function setGlobalPropertyValues(): void
    {
        $this->public_resource_types = (new ResourceType())->publicResourceTypes();

        $this->include_public = true;
        if (Boolean::convertedValue(request()->query('exclude-public')) === true) {
            $this->include_public = false;
        }

        if (auth('api')->user() !== null && auth()->guard('api')->check() === true) {
            $this->user_id = auth('api')->user()->id;
            $this->permitted_resource_types = $this->resource_type_access->permittedResourceTypes($this->user_id);
        }
    }

    protected function permittedUsers(int $resource_type_id): ?array
    {
        return $this->resource_type_access->permittedResourceTypeUsers($resource_type_id, $this->user_id);
    }
}
