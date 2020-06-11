<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\Models\Summary\Subcategory;
use App\Option\Get;
use App\Response\Header\Header;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Summary controller for the subcategories routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubcategoryController extends Controller
{
    /**
     * Return a summary of the subcategories
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function index($resource_type_id, $category_id): JsonResponse
    {
        \App\Request\Route\Validate::category(
            $resource_type_id,
            $category_id,
            $this->permitted_resource_types
        );

        $search_parameters = \App\Request\Parameter\Search::fetch(
            array_keys(Config::get('api.subcategory.summary-searchable'))
        );

        $summary = (new Subcategory())->totalCount(
            $resource_type_id,
            $category_id,
            $search_parameters
        );

        $headers = new Header();
        $headers->add('X-Total-Count', $summary);
        $headers->add('X-Count', $summary);

        return response()->json(
            [
                'subcategories' => $summary
            ],
            200,
            $headers->headers()
        );
    }


    /**
     * Generate the OPTIONS request for the subcategories summary
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function optionsIndex($resource_type_id, $category_id): JsonResponse
    {
        \App\Request\Route\Validate::category(
            $resource_type_id,
            $category_id,
            $this->permitted_resource_types
        );

        $permissions = \App\Request\Route\Permission::category(
            $resource_type_id,
            $category_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setDescription('route-descriptions.summary_subcategory_GET_index')->
            setAuthenticationStatus($permissions['view'])->
            setSearchable('api.subcategory.summary-searchable')->
            option();

        return $this->optionsResponse($get, 200);
    }
}
