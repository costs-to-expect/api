<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Route\Validate;
use App\Models\Item;
use App\Models\Transformers\ItemCategorySummary as ItemCategorySummaryTransformer;
use App\Models\Transformers\ItemSubCategorySummary as ItemSubCategorySummaryTransformer;
use App\Utilities\Response as UtilityResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Category summary
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryCategoryController extends Controller
{
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
    ): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

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
     */
    public function optionsCategories(Request $request, string $resource_type_id, string $resource_id)
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        return $this->generateOptionsForIndex(
            [
                'description_localisation' => 'route-descriptions.summary_GET_categories',
                'parameters_config' => [],
                'conditionals' => [],
                'pagination' => false,
                'authenticated' => false
            ]
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
    ): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        Validate::categoryRoute($category_id);

        $category_summary = (new Item())->categorySummary(
            $resource_type_id,
            $resource_id,
            $category_id
        );

        if (count($category_summary) !== 1) {
            UtilityResponse::notFound();
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
     */
    public function optionsCategory(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id
    ) {
        Validate::resourceRoute($resource_type_id, $resource_id);

        Validate::categoryRoute($category_id);

        return $this->generateOptionsForShow(
            [
                'description_localisation' => 'route-descriptions.summary_GET_category',
                'parameters_config' => [],
                'conditionals' => [],
                'authenticated' => false
            ]
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
    ): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        Validate::categoryRoute($category_id);

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
     */
    public function optionsSubCategories(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id
    ) {
        Validate::resourceRoute($resource_type_id, $resource_id);

        Validate::categoryRoute($category_id);

        return $this->generateOptionsForIndex(
            [
                'description_localisation' => 'route-descriptions.summary_GET_sub_categories',
                'parameters_config' => [],
                'conditionals' => [],
                'pagination' => false,
                'authenticated' => false
            ]
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
    ): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        Validate::subCategoryRoute($category_id, $sub_category_id);

        $sub_category_summary = (new Item())->subCategorySummary(
            $resource_type_id,
            $resource_id,
            $category_id,
            $sub_category_id
        );

        if (count($sub_category_summary) !== 1) {
            UtilityResponse::notFound();
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
     */
    public function optionsSubCategory(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id,
        string $sub_category_id
    ) {
        Validate::resourceRoute($resource_type_id, $resource_id);

        Validate::subCategoryRoute($category_id, $sub_category_id);

        return $this->generateOptionsForShow(
            [
                'description_localisation' => 'route-descriptions.summary_GET_sub_category',
                'parameters_config' => [],
                'conditionals' => [],
                'authenticated' => false
            ]
        );
    }
}
