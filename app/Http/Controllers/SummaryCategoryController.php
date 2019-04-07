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
