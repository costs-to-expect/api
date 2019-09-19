<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Option\Get;
use App\Utilities\Header;
use Illuminate\Http\JsonResponse;

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
     * Return a summary of the categories
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $summary = (new Category())->totalCount(
            $this->permitted_resource_types,
            $this->include_public
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
     * @return JsonResponse
     */
    public function optionsIndex(): JsonResponse
    {
        $get = Get::init()->
            setDescription('route-descriptions.summary_category_GET_index')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            option();

        return $this->optionsResponse($get, 200);
    }
}
