<?php

namespace App\Http\Controllers;

use App\Item\AbstractItem;
use App\Item\ItemFactory;
use App\Models\PermittedUser;
use App\Utilities\Hash;
use Exception;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var Hash
     */
    protected $hash;

    /**
     * @var boolean Include public content
     */
    protected $include_public;

    /**
     * @var array Permitted resource types
     */
    protected $permitted_resource_types = [];

    /**
     * @var integer|null
     */
    protected $user_id = null;

    /**
     * @var boolean Allow the entire collection to be returned ignoring pagination
     */
    protected $allow_entire_collection = false;

    /**
     * @var AbstractItem
     */
    protected $item_interface = null;

    public function __construct()
    {
        $this->hash = new Hash();

        $this->middleware(function ($request, $next) {
            $this->setHelperProperties();

            return $next($request);
        });
    }

    protected function setHelperProperties()
    {
        if (auth()->guard('api')->check() === true && auth('api')->user() !== null) {
            $this->user_id = auth('api')->user()->id;
            $this->permitted_resource_types = (new PermittedUser())->permittedResourceTypes($this->user_id);
        }

        $this->include_public = true;
    }

    /**
     * Generate and return the options response
     *
     * @param array $verbs Verb arrays
     * @param integer $http_status_code, defaults to 200
     *
     * @return JsonResponse
     */
    protected function optionsResponse(array $verbs, $http_status_code = 200): JsonResponse
    {
        $options = [
            'verbs' => [],
            'http_status_code' => $http_status_code,
            'headers' => [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Header' => 'X-Requested-With, Origin, Content-Type, Accept, Authorization',
                'Access-Control-Allow-Methods' => implode(', ', array_keys($verbs)) . ', OPTIONS',
                'Content-Type' => 'application/json'
            ]
        ];

        foreach ($verbs as $verb => $detail) {
            $options['verbs'][$verb] = $detail;
        }

        response()->json(
            $options['verbs'],
            $options['http_status_code'],
            $options['headers']
        )->send();
        exit;
    }

    /**
     * Make a call to the item interface factory and set the relevant item
     * interface
     *
     * @param integer $resource_type_id
     */
    protected function setItemInterface(int $resource_type_id)
    {
        try {
            $this->item_interface = ItemFactory::getItemInterface($resource_type_id);
        } catch (Exception $e) {
            abort(500, $e->getMessage());
        }
    }
}
