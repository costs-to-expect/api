<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Option\Get;
use App\Validators\Request\Route;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Summary controller for the subcategories routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummarySubcategoryController extends Controller
{
    /**
     * Return a summary of the subcategories
     *
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function index(string $category_id): JsonResponse
    {
        Route::category(
            $category_id,
            $this->permitted_resource_types
        );

        $summary = (new SubCategory())->totalCount($category_id);

        return response()->json(
            [
                'subcategories' => $summary
            ],
            200,
            [
                'X-Total-Count' => $summary,
                'X-Count' => $summary
            ]
        );
    }


    /**
     * Generate the OPTIONS request for the subcategories summary
     *
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(string $category_id): JsonResponse
    {
        Route::category(
            $category_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setDescription('route-descriptions.summary_subcategory_GET_index')->
            option();

        return $this->optionsResponse($get, 200);
    }
}
