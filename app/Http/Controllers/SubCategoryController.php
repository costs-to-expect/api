<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Transformers\SubCategory as SubCategoryTransformer;
use App\Validators\SubCategory as SubCategoryValidator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage category sub categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubCategoryController extends Controller
{
    /**
     * Return all the sub categories assigned to the given category
     *
     * @param Request $request
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function index(Request $request, string $category_id): JsonResponse
    {
        $sub_categories = (new SubCategory())
            ->where('category_id', '=', $category_id)
            ->get();

        $headers = [
            'X-Total-Count' => count($sub_categories)
        ];

        $link = $this->generateLinkHeader(10, 0, 20);
        if ($link !== null) {
            $headers['Link'] = $link;
        }

        return response()->json(
            $sub_categories->map(
                function ($sub_category)
                {
                    return (new SubCategoryTransformer($sub_category))->toArray();
                }
            ),
            200,
            $headers
        );
    }

    /**
     * Return a single sub category
     *
     * @param Request $request
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return JsonResponse
     */
    public function show(Request $request, string $category_id, string $sub_category_id): JsonResponse
    {
        $sub_category = (new SubCategory())
            ->where('category_id', '=', $category_id)
            ->find($sub_category_id);

        if ($sub_category === null) {
            return $this->returnResourceNotFound();
        }

        return response()->json(
            (new SubCategoryTransformer($sub_category))->toArray(),
            200,
            [
                'X-Total-Count' => 1
            ]
        );
    }

    /**
     * Generate the OPTIONS request for the sub categories list
     *
     * @param Request $request
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(Request $request, string $category_id): JsonResponse
    {
        return $this->generateOptionsForIndex(
            'descriptions.sub_category.GET_index',
            'descriptions.sub_category.POST',
            'routes.sub_category.fields',
            'routes.sub_category.parameters'
        );
    }

    /**
     * Generate the OPTIONS request for the specific sub category
     *
     * @param Request $request
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return JsonResponse
     */
    public function optionsShow(Request $request, string $category_id, string $sub_category_id): JsonResponse
    {
        return $this->generateOptionsForShow(
            'descriptions.sub_category.GET_show',
            'descriptions.sub_category.DELETE',
            'descriptions.sub_category.PATCH',
            'routes.sub_category.fields'
        );
    }

    /**
     * Create a new sub category
     *
     * @param Request $request
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function create(Request $request, string $category_id): JsonResponse
    {
        $validator = (new SubCategoryValidator)->create($request, $category_id);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $sub_category = new SubCategory([
                'category_id' => $category_id,
                'name' => $request->input('name'),
                'description' => $request->input('description')
            ]);
            $sub_category->save();
        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => 'Error creating new record'
                ],
                500
            );
        }

        return response()->json(
            (new SubCategoryTransformer($sub_category))->toArray(),
            201
        );
    }

    /**
     * Delete a sub category
     *
     * @param Request $request
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return JsonResponse
     */
    public function delete(Request $request, string $category_id, string $sub_category_id): JsonResponse
    {
        return response()->json(null,204);
    }

    /**
     * Update the request sub category
     *
     * @param Request $request
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return JsonResponse
     */
    public function update(Request $request, string $category_id, string $sub_category_id): JsonResponse
    {
        $validator = (new SubCategoryValidator)->update($request, $category_id, $sub_category_id);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        if (count($request->all()) === 0) {
            return $this->requireAtLeastOneFieldToPatch();
        }

        return response()->json(
            [
                'category_id' => $category_id,
                'sub_category_id' => $sub_category_id
            ],
            200
        );
    }
}
