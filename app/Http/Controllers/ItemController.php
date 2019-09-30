<?php

namespace App\Http\Controllers;

use App\Models\ItemTypeAllocatedExpense;
use App\Option\Delete;
use App\Option\Get;
use App\Option\Patch;
use App\Option\Post;
use App\Utilities\Header;
use App\Utilities\RoutePermission;
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
        Route::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
        );

        $this->setItemInterface($resource_type_id);

        $parameters = Parameters::fetch(
            array_merge(
                array_keys(Config::get('api.item.parameters.collection')),
                array_keys($this->item_interface->collectionParameters())
            )
        );

        $item_model = $this->item_interface->model();

        $search_parameters = SearchParameters::fetch(
            $this->item_interface->searchParameters()
        );

        $total = $item_model->totalCount(
            $resource_type_id,
            $resource_id,
            $parameters,
            $search_parameters
        );

        $sort_parameters = SortParameters::fetch(
            $this->item_interface->sortParameters()
        );

        $pagination = UtilityPagination::init(request()->path(), $total)
            ->setParameters($parameters)
            ->setSortParameters($sort_parameters)
            ->setSearchParameters($search_parameters)
            ->paging();

        $items = $item_model->paginatedCollection(
            $resource_type_id,
            $resource_id,
            $pagination['offset'],
            $pagination['limit'],
            $parameters,
            $sort_parameters,
            $search_parameters
        );

        $headers = new Header();
        $headers->collection($pagination, count($items), $total);

        $sort_header = SortParameters::xHeader();
        if ($sort_header !== null) {
            $headers->addSort($sort_header);
        }

        $search_header = SearchParameters::xHeader();
        if ($search_header !== null) {
            $headers->addSearch($search_header);
        }

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            array_map(
                function($item) {
                    return (new ItemTransformer($item))->toArray();
                },
                $items
            ),
            200,
            $headers->headers()
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
        Route::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        $item = (new Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            UtilityResponse::notFound(trans('entities.item'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new ItemTransformer($item))->toArray(),
            200,
            $headers->headers()
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
        Route::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
        );

        $this->setItemInterface($resource_type_id);

        $permissions = RoutePermission::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
        );

        $parameters = Parameters::fetch(
            array_merge(
                array_keys(Config::get('api.item.parameters.collection')),
                array_keys($this->item_interface->collectionParameters())
            )
        );

        $conditional_parameters = $this->conditionalParameters(
            $resource_type_id,
            $parameters
        );

        $get = Get::init()->
            setSortable($this->item_interface->sortParametersConfig())->
            setSearchable($this->item_interface->searchParametersConfig())->
            setParameters(
                $this->item_interface->collectionParametersConfig(),
                true
            )->
            setConditionalParameters($conditional_parameters)->
            setPagination(true)->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.item_GET_index')->
            option();

        $post = Post::init()->
            setFields($this->item_interface->postFieldsConfig())->
            setDescription( 'route-descriptions.item_POST')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
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
            $this->permitted_resource_types,
        );

        $this->setItemInterface($resource_type_id);

        $item = (new Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            UtilityResponse::notFound(trans('entities.item'));
        }

        $get = Get::init()->
            setParameters($this->item_interface->showParametersConfig())->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.item_GET_show')->
            option();

        $delete = Delete::init()->
            setDescription('route-descriptions.item_DELETE')->
            setAuthenticationStatus($permissions['manage'])->
            setAuthenticationRequired(true)->
            option();

        $patch = Patch::init()->
            setFields($this->item_interface->postFieldsConfig())->
            setDescription('route-descriptions.item_PATCH')->
            setAuthenticationStatus($permissions['manage'])->
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
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function create(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        Route::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
            true
        );

        $this->setItemInterface($resource_type_id);

        $validator = (new ItemValidator)->create();
        UtilityRequest::validateAndReturnErrors($validator);

        try {
            $item = new Item([
                'resource_id' => $resource_id,
                'created_by' => Auth::user()->id
            ]);
            $item->save();

            $item_type = $this->item_interface->create((int) $item->id);

        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ItemTransformer((new Item())->instanceToArray($item, $item_type)))->toArray(),
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
        Route::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types,
            true
        );

        $this->setItemInterface($resource_type_id);

        UtilityRequest::checkForEmptyPatch();

        UtilityRequest::checkForInvalidFields(
            (new Item())->patchableFields()
        );

        $validator = (new ItemValidator)->update();
        UtilityRequest::validateAndReturnErrors($validator);

        $item = (new Item())->instance($resource_type_id, $resource_id, $item_id);
        $item_type = $this->item_interface->instance((int) $item_id);

        if ($item === null || $item_type === null) {
            UtilityResponse::failedToSelectModelForUpdate();
        }

        try {
            $item->updated_by = Auth::user()->id;

            if ($item->save() === true) {
                $this->item_interface->update(request()->all(), $item_type);
            }
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForUpdate();
        }

        UtilityResponse::successNoContent();
    }

    /**
     * Delete the assigned item
     *
     * @param string $resource_type_id,
     * @param string $resource_id,
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function delete(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        Route::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
            true
        );

        $item_type = (new ItemTypeAllocatedExpense())->instance($item_id);
        $item = (new Item())->instance($resource_type_id, $resource_id, $item_id);

        if ($item === null || $item_type === null) {
            UtilityResponse::notFound(trans('entities.item'));
        }

        try {
            $item_type->delete();
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
                'description' => trans('item-type-allocated-expense/allowed-values.description-prefix-year') . $i
            ];
        }

        for ($i=1; $i < 13; $i++) {
            $conditional_parameters['month']['allowed_values'][$i] = [
                'value' => $i,
                'name' => date("F", mktime(0, 0, 0, $i, 10)),
                'description' => trans('item-type-allocated-expense/allowed-values.description-prefix-month') .
                    date("F", mktime(0, 0, 0, $i, 1))
            ];
        }

        $categories = (new Category())->paginatedCollection(
            $resource_type_id,
            $this->permitted_resource_types,
            $this->include_public,
            0,
            100
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
