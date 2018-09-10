<?php

namespace App\Http\Controllers;

use App\Http\Route\Validators\Resource as ResourceRouteValidator;
use App\Models\Item;
use App\Transformers\ItemCategorySummary as ItemCategorySummaryTransformer;
use App\Transformers\ItemSubCategorySummary as ItemSubCategorySummaryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Resource summary
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryController extends Controller
{
    /**
     * Return the TCO for the resource
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function tco(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $summary = (new Item())->summary($resource_type_id, $resource_id);

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            [
                'total' => number_format($summary, 2, '.', '')
            ],
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for the TCO
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsTco(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $routes = [
            'GET' => [
                'description' => Config::get('api.descriptions.summary.GET_tco'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $options_response = $this->generateOptionsResponse($routes);

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }

    /**
     * Return the categories summary for a resource
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function categories(
        Request $request,
        string $resource_type_id,
        string $resource_id
    ): JsonResponse {
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $summary = (new Item())->categoriesSummary(
            $resource_type_id,
            $resource_id
        );

        $headers = [
            'X-Total-Count' => count($summary)
        ];

        return response()->json(
            $summary->map(
                function ($category_summary) {
                    return (new ItemCategorySummaryTransformer($category_summary))->toArray();
                }
            ),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for the categories summary
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsCategories(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $routes = [
            'GET' => [
                'description' => Config::get('api.descriptions.summary.GET_categories'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $options_response = $this->generateOptionsResponse($routes);

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }

    /**
     * Return the resource category summary for a specific category
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function category(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id
    ): JsonResponse {
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $category_summary = (new Item())->categorySummary(
            $resource_type_id,
            $resource_id,
            $category_id
        );

        if (count($category_summary) !== 1) {
            return $this->returnResourceNotFound();
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            (new ItemCategorySummaryTransformer($category_summary[0]))->toArray(),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for a category summary
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsCategory(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id
    ): JsonResponse {
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $routes = [
            'GET' => [
                'description' => Config::get('api.descriptions.summary.GET_category'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $options_response = $this->generateOptionsResponse($routes);

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }

    /**
     * Return the sub categories summary for a resource and category
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function subCategories(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id
    ): JsonResponse {
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $summary = (new Item())->subCategoriesSummary(
            $resource_type_id,
            $resource_id,
            $category_id
        );

        $headers = [
            'X-Total-Count' => count($summary)
        ];

        return response()->json(
            $summary->map(
                function ($category_summary) {
                    return (new ItemSubCategorySummaryTransformer($category_summary))->toArray();
                }
            ),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for the sub categories summary
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsSubCategories(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id
    ): JsonResponse {
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $routes = [
            'GET' => [
                'description' => Config::get('api.descriptions.summary.GET_sub_categories'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $options_response = $this->generateOptionsResponse($routes);

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }

    /**
     * Return the resource sub category summary for a specific sub category
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return JsonResponse
     */
    public function subCategory(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id,
        string $sub_category_id
    ): JsonResponse {
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $sub_category_summary = (new Item())->subCategorySummary(
            $resource_type_id,
            $resource_id,
            $category_id,
            $sub_category_id
        );

        if (count($sub_category_summary) !== 1) {
            return $this->returnResourceNotFound();
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            (new ItemSubCategorySummaryTransformer($sub_category_summary[0]))->toArray(),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for the sub category summary
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsSubCategory(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id,
        string $sub_category_id
    ): JsonResponse {
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $routes = [
            'GET' => [
                'description' => Config::get('api.descriptions.summary.GET_sub_category'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $options_response = $this->generateOptionsResponse($routes);

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }
}
