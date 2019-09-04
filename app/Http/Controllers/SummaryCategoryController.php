<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Option\Get;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Summary controller for the categories routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryCategoryController extends Controller
{
    /**
     * Return a summary of the resource types
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $summary = (new Category())->totalCount($this->include_private);

        return response()->json(
            [
                'categories' => $summary
            ],
            200,
            [
                'X-Total-Count' => $summary,
                'X-Count' => $summary
            ]
        );
    }


    /**
     * Generate the OPTIONS request for the categories summary
     *
     * @return JsonResponse
     */
    public function optionsIndex(): JsonResponse
    {
        $get = Get::init()->
            setDescription('route-descriptions.summary_category_GET_index')->
            option();

        return $this->optionsResponse($get, 200);
    }
}
