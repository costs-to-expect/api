<?php

namespace App\Http\Controllers;

use App\Models\ResourceType;
use App\Models\ResourceTypeAccess;
use App\Request\Hash;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected Hash $hash;

    protected bool $include_public;

    protected array $permitted_resource_types = [];

    protected array $public_resource_types = [];

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

        if (auth('api')->user() !== null && auth()->guard('api')->check() === true) {
            $this->user_id = auth('api')->user()->id;
            $this->permitted_resource_types = (new ResourceTypeAccess())->permittedResourceTypes($this->user_id);
        }
    }
}
