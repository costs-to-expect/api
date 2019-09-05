<?php

namespace App\Http\Controllers;

use App\Option\Delete;
use App\Option\Get;
use App\Option\Patch;
use App\Option\Post;
use App\Validators\Request\Parameters;
use App\Validators\Request\Route;
use App\Models\Category;
use App\Models\Item;
use App\Models\SubCategory;
use App\Models\Transformers\Item as ItemTransformer;
use App\Utilities\Pagination as UtilityPagination;
use App\Utilities\Request as UtilityRequest;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Request\Fields\Item as ItemValidator;
use App\Validators\Request\SearchParameters;
use App\Validators\Request\SortParameters;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * Manage items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemController extends Controller
{
    /**
     * Return all the items based on the set filter options
     *
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function index(string $resource_type_id, string $resource_id): JsonResponse
    {
        Route::resourceRoute($resource_type_id, $resource_id);

        $parameters = Parameters::fetch([
            'include-categories',
            'include-subcategories',
            'include-unpublished',
            'year',
            'month',
            'category',
            'subcategory'
        ]);

        $search_parameters = SearchParameters::fetch(
            Config::get('api.item.searchable')
        );

        $total = (new Item())->totalCount(
            $resource_type_id,
            $resource_id,
            $parameters,
            $search_parameters
        );

        $sort_parameters = SortParameters::fetch(
            Config::get('api.item.sortable')
        );

        $pagination = UtilityPagination::init(request()->path(), $total)
            ->setParameters($parameters)
            ->setSortParameters($sort_parameters)
            ->setSearchParameters($search_parameters)
            ->paging();

        $items = (new Item())->paginatedCollection(
            $resource_type_id,
            $resource_id,
            $pagination['offset'],
            $pagination['limit'],
            $parameters,
            $sort_parameters,
            $search_parameters
        );

        $headers = [
            'X-Count' => count($items),
            'X-Total-Count' => $total,
            'X-Offset' => $pagination['offset'],
            'X-Limit' => $pagination['limit'],
            'X-Link-Previous' => $pagination['links']['previous'],
            'X-Link-Next' => $pagination['links']['next']
        ];

        $sort_header = SortParameters::xHeader();
        if ($sort_header !== null) {
            $headers['X-Sort'] = $sort_header;
        }

        $search_header = SearchParameters::xHeader();
        if ($search_header !== null) {
            $headers['X-Search'] = $search_header;
        }

        return response()->json(
            array_map(
                function($item) {
                    return (new ItemTransformer($item))->toArray();
                },
                $items
            ),
            200,
            $headers
        );
    }

    /**
     * Return a single item
     *
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function show(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        Route::itemRoute($resource_type_id, $resource_id, $item_id);

        $item = (new Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            UtilityResponse::notFound(trans('entities.item'));
        }

        return response()->json(
            (new ItemTransformer($item))->toArray(),
            200,
            [
                'X-Total-Count' => 1,
                'X-Count' => 1
            ]
        );
    }

    /**
     * Generate the OPTIONS request for the item list
     *
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        Route::resourceRoute($resource_type_id, $resource_id);

        $parameters = Parameters::fetch(['year', 'month', 'category', 'subcategory']);

        $conditional_parameters = $this->conditionalParameters(
            $resource_type_id,
            $parameters
        );

        $get = Get::init()->
            setDescription('route-descriptions.item_GET_index')->
            setPagination(true)->
            setSortable('api.item.sortable')->
            setSearchable('api.item.searchable')->
            setParameters('api.item.parameters.collection')->
            setConditionalParameters($conditional_parameters)->
            option();

        $post = Post::init()->
            setDescription( 'route-descriptions.item_POST')->
            setAuthenticationRequired(true)->
            setFields('api.item.fields')->
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
     *
     * @return JsonResponse
     */
    public function optionsShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        Route::itemRoute($resource_type_id, $resource_id, $item_id);

        $item = (new Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            UtilityResponse::notFound(trans('entities.item'));
        }

        $get = Get::init()->
            setDescription('route-descriptions.item_GET_show')->
            setParameters('api.item.parameters.item')->
            option();

        $delete = Delete::init()->
            setDescription('route-descriptions.item_DELETE')->
            setAuthenticationRequired(true)->
            option();

        $patch = Patch::init()->
            setDescription('route-descriptions.item_PATCH')->
            setFields('api.item.fields')->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $delete + $patch,
            200
        );
    }

    /**
     * Create a new item
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function create(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        Route::resourceRoute($resource_type_id, $resource_id);

        $validator = (new ItemValidator)->create();
        UtilityRequest::validateAndReturnErrors($validator);

        try {
            $item = new Item([
                'resource_id' => $resource_id,
                'description' => $request->input('description'),
                'effective_date' => $request->input('effective_date'),
                'publish_after' => $request->input('publish_after', null),
                'total' => $request->input('total'),
                'percentage' => $request->input('percentage', 100),
                'user_id' => Auth::user()->id
            ]);
            $item->setActualisedTotal($item->total, $item->percentage);
            $item->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ItemTransformer((new Item())->instanceToArray($item)))->toArray(),
            201
        );
    }

    /**
     * Update the selected item
     *
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function update(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        Route::itemRoute($resource_type_id, $resource_id, $item_id);

        UtilityRequest::checkForEmptyPatch();

        UtilityRequest::checkForInvalidFields((new Item())->patchableFields());

        $validator = (new ItemValidator)->update();
        UtilityRequest::validateAndReturnErrors($validator);

        $item = (new Item())->instance($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            UtilityResponse::failedToSelectModelForUpdate();
        }

        $update_actualised = false;
        foreach (request()->all() as $key => $value) {
            $item->$key = $value;

            if (in_array($key, ['total', 'percentage']) === true) {
                $update_actualised = true;
            }
        }

        if ($update_actualised === true) {
            $item->setActualisedTotal($item->total, $item->percentage);
        }

        try {
            $item->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForUpdate();
        }

        UtilityResponse::successNoContent();
    }

    /**
     * Delete the assigned item
     *
     * @param Request $request,
     * @param string $resource_type_id,
     * @param string $resource_id,
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function delete(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        Route::resourceRoute($resource_type_id, $resource_id);

        $item = (new Item())->instance($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            UtilityResponse::notFound(trans('entities.item'));
        }

        try {
            $item->delete();

            UtilityResponse::successNoContent();
        } catch (QueryException $e) {
            UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityResponse::notFound(trans('entities.item'));
        }
    }

    /**
     * Set any conditional GET parameters, these will be merged with the data arrays defined in
     * config/api/[item-type]/parameters.php
     *
     * @param integer $resource_type_id
     * @param array $parameters
     *
     * @return array
     */
    private function conditionalParameters(
        int $resource_type_id,
        array $parameters
    ): array
    {
        $conditional_parameters = [
            'year' => [
                'allowed_values' => []
            ],
            'month' => [
                'allowed_values' => []
            ],
            'category' => [
                'allowed_values' => []
            ]
        ];

        for ($i=2013; $i <= intval(date('Y')); $i++) {
            $conditional_parameters['year']['allowed_values'][$i] = [
                'value' => $i,
                'name' => $i,
                'description' => trans('item/allowed-values.description-prefix-year') . $i
            ];
        }

        for ($i=1; $i < 13; $i++) {
            $conditional_parameters['month']['allowed_values'][$i] = [
                'value' => $i,
                'name' => date("F", mktime(0, 0, 0, $i, 10)),
                'description' => trans('item/allowed-values.description-prefix-month') .
                    date("F", mktime(0, 0, 0, $i, 1))
            ];
        }

        $categories = (new Category())->paginatedCollection(
            $this->include_private,
            0,
            100,
            ['resource_type'=>$resource_type_id]
        );

        foreach ($categories as $category) {
            $conditional_parameters['category']['allowed_values'][$this->hash->encode('category', $category['category_id'])] = [
                'value' => $this->hash->encode('category', $category['category_id']),
                'name' => $category['category_name'],
                'description' => trans('item/allowed-values.description-prefix-category') .
                    $category['category_name'] . trans('item/allowed-values.description-suffix-category')
            ];
        }

        if (array_key_exists('category', $parameters) === true) {

            $subcategories = (new SubCategory())->paginatedCollection($parameters['category']);

            array_map(
                function($subcategory) use (&$conditional_parameters) {
                    $conditional_parameters['subcategory']['allowed_values'][$this->hash->encode('subcategory', $subcategory['id'])] = [
                        'value' => $this->hash->encode('subcategory', $subcategory['id']),
                        'name' => $subcategory['name'],
                        'description' => trans('item/allowed-values.description-prefix-subcategory') .
                            $subcategory['name'] . trans('item/allowed-values.description-suffix-subcategory')
                    ];
                },
                $subcategories
            );
        }

        return $conditional_parameters;
    }
}
