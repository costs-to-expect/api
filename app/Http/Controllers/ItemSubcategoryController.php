<?php

namespace App\Http\Controllers;

use App\Option\Delete;
use App\Option\Get;
use App\Option\Post;
use App\Utilities\Header;
use App\Utilities\RoutePermission;
use App\Validators\Request\Route;
use App\Models\ItemCategory;
use App\Models\ItemSubcategory;
use App\Models\Subcategory;
use App\Models\Transformers\ItemSubcategory as ItemSubcategoryTransformer;
use App\Utilities\Request as UtilityRequest;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Request\Fields\ItemSubcategory as ItemSubcategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage the category for an item row
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubcategoryController extends Controller
{
    /**
     * Return the sub category assigned to an item
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function index(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        Route::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        if ($item_category_id === 'nill') {
            UtilityResponse::notFound(trans('entities.item-subcategory'));
        }

        $item_sub_category = (new ItemSubcategory())->paginatedCollection(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_sub_category === null || (is_array($item_sub_category) && count($item_sub_category) === 0)) {
            UtilityResponse::notFound(trans('entities.item-subcategory'));
        }

        $headers = new Header();
        $headers->add('X-Total-Count', 1);
        $headers->add('X-Count', 1);

        return response()->json(
            [(new ItemSubcategoryTransformer($item_sub_category[0]))->toArray()],
            200,
            $headers->headers()
        );
    }

    /**
     * Return a single item
     *
     * @param Request $request
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     * @param string $item_category_id
     * @param string $item_subcategory_id
     *
     * @return JsonResponse
     */
    public function show(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $item_subcategory_id
    ): JsonResponse
    {
        Route::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        if ($item_category_id === 'nill' || $item_subcategory_id === 'nill') {
            UtilityResponse::notFound(trans('entities.item-subcategory'));
        }

        $item_sub_category = (new ItemSubcategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_subcategory_id
        );

        if ($item_sub_category === null) {
            UtilityResponse::notFound(trans('entities.item-subcategory'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new ItemSubcategoryTransformer($item_sub_category))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the item list
     *
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        Route::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        $permissions = RoutePermission::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        if ($item_category_id === 'nill') {
            UtilityResponse::notFound(trans('entities.item-subcategory'));
        }

        $item_category = (new ItemCategory())->find($item_category_id);
        if ($item_category === null) {
            UtilityResponse::notFound(trans('entities.item-category'));
        }

        $get = Get::init()->
            setParameters('api.item-subcategory.parameters.collection')->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.item_sub_category_GET_index')->
            option();

        $post = Post::init()->
            setFields('api.item-subcategory.fields')->
            setConditionalFields($this->conditionalPostParameters($item_category->category_id))->
            setDescription('route-descriptions.item_sub_category_POST')->
            setAuthenticationStatus($permissions['manage'])->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $post,
            200
        );
    }

    /**
     * Generate the OPTIONS request for a specific item
     *
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     * @param string $item_category_id
     * @param string $item_subcategory_id
     *
     * @return JsonResponse
     */
    public function optionsShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $item_subcategory_id
    ): JsonResponse
    {
        Route::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        $permissions = RoutePermission::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        if ($item_category_id === 'nill' || $item_subcategory_id === 'nill') {
            UtilityResponse::notFound(trans('entities.item-subcategory'));
        }

        $item_sub_category = (new ItemSubcategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_subcategory_id
        );

        if ($item_sub_category === null) {
            UtilityResponse::notFound(trans('entities.item-subcategory'));
        }

        $get = Get::init()->
            setParameters('api.item-subcategory.parameters.item')->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.item_sub_category_GET_show')->
            option();

        $delete = Delete::init()->
            setDescription('route-descriptions.item_sub_category_DELETE')->
            setAuthenticationStatus($permissions['manage'])->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $delete,
            200
        );
    }

    /**
     * Assign the sub category
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function create(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        Route::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types,
            true
        );

        if ($item_category_id === 'nill') {
            UtilityResponse::notFound(trans('entities.item-subcategory'));
        }

        $item_category = (new ItemCategory())
            ->where('item_id', '=', $item_id)
            ->find($item_category_id);

        $validator = (new ItemSubcategoryValidator)->create(['category_id' => $item_category->category_id]);
        UtilityRequest::validateAndReturnErrors(
            $validator,
            $this->conditionalPostParameters($item_category_id)
        );

        try {
            $subcategory_id = $this->hash->decode('subcategory', $request->input('subcategory_id'));

            if ($subcategory_id === false) {
                UtilityResponse::unableToDecode();
            }

            $item_sub_category = new ItemSubcategory([
                'item_category_id' => $item_category_id,
                'sub_category_id' => $subcategory_id
            ]);
            $item_sub_category->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ItemSubcategoryTransformer((new ItemSubcategory())->instanceToArray($item_sub_category)))->toArray(),
            201
        );
    }

    /**
     * Generate any conditional POST parameters, will be merged with the data
     * arrays defined in config/api/[type]/fields.php
     *
     * @param integer $category_id
     *
     * @return array
     */
    private function conditionalPostParameters($category_id): array
    {
        $sub_categories = (new Subcategory())
            ->select('id', 'name', 'description')
            ->where('category_id', '=', $category_id)
            ->get();

        $conditional_post_parameters = ['subcategory_id' => []];

        foreach ($sub_categories as $sub_category) {
            $id = $this->hash->encode('subcategory', $sub_category->id);

            if ($id === false) {
                UtilityResponse::unableToDecode();
            }

            $conditional_post_parameters['subcategory_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $sub_category->name,
                'description' => $sub_category->description
            ];
        }

        return $conditional_post_parameters;
    }

    /**
     * Delete the assigned sub category
     *
     * @param Request $request,
     * @param string $resource_type_id,
     * @param string $resource_id,
     * @param string $item_id,
     * @param string $item_category_id,
     * @param string $item_subcategory_id
     *
     * @return JsonResponse
     */
    public function delete(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $item_subcategory_id
    ): JsonResponse
    {
        Route::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types,
            true
        );

        if ($item_category_id === 'nill' || $item_subcategory_id === 'nill') {
            UtilityResponse::notFound(trans('entities.item-subcategory'));
        }

        $item_sub_category = (new ItemSubcategory())->instance(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_subcategory_id
        );

        if ($item_sub_category === null) {
            UtilityResponse::notFound(trans('entities.item-subcategory'));
        }


        try {
            (new ItemSubcategory())->find($item_subcategory_id)->delete();

            UtilityResponse::successNoContent();
        } catch (QueryException $e) {
            UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityResponse::notFound(trans('entities.item-subcategory'), $e);
        }
    }
}
