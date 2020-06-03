<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\Models\Summary\Category;
use App\Option\Get;
use App\Utilities\Header;
use App\Utilities\RoutePermission;
use App\Validators\Route;
use App\Validators\SearchParameters;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Summary controller for the categories routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class CategoryController extends Controller
{
    /**
     * Return a summary of the categories
     *
     * @param $resource_type_id
     *
     * @return JsonResponse
     */
    public function index($resource_type_id): JsonResponse
    {
        Route::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $search_parameters = SearchParameters::fetch(
            array_keys(Config::get('api.category.summary-searchable'))
        );

        $summary = (new Category())->total(
            $resource_type_id,
            $this->permitted_resource_types,
            $this->include_public,
            $search_parameters
        );

        $headers = new Header();
        $headers->add('X-Total-Count', $summary);
        $headers->add('X-Count', $summary);

        return response()->json(
            [
                'categories' => $summary
            ],
            200,
            $headers->headers()
        );
    }


    /**
     * Generate the OPTIONS request for the categories summary
     *
     * @param $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex($resource_type_id): JsonResponse
    {
        Route::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = RoutePermission::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setDescription('route-descriptions.summary_category_GET_index')->
            setAuthenticationStatus($permissions['view'])->
            setSearchable('api.category.summary-searchable')->
            option();

        return $this->optionsResponse($get, 200);
    }
}
