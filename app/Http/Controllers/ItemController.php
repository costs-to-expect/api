<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Get;
use App\Http\Parameters\Route\Validate;
use App\Models\Category;
use App\Models\Item;
use App\Models\SubCategory;
use App\Models\Transformers\Item as ItemTransformer;
use App\Utilities\Pagination as UtilityPagination;
use App\Utilities\Response as UtilityResponse;
use App\Http\Parameters\Request\Validators\Item as ItemValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemController extends Controller
{
    protected $collection_parameters = [];
    protected $get_parameters = [];
    protected $pagination = [];

    /**
     * Return all the items based on the set filter options
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function index(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $this->collection_parameters = Get::parameters(['year', 'month', 'category', 'sub_category']);

        $total = (new Item())->totalCount(
            $resource_type_id,
            $resource_id,
            $this->collection_parameters
        );

        $pagination = UtilityPagination::init($request->path(), $total)
            ->setParameters($this->collection_parameters)
            ->paging();

        $items = (new Item())->paginatedCollection(
            $resource_type_id,
            $resource_id,
            $pagination['offset'],
            $pagination['limit'],
            $this->collection_parameters
        );

        $headers = [
            'X-Count' => count($items),
            'X-Total-Count' => $total,
            'X-Link-Previous' => $pagination['links']['previous'],
            'X-Link-Next' => $pagination['links']['next']
        ];

        return response()->json(
            $items->map(
                function ($item)
                {
                    return (new ItemTransformer($item))->toArray();
                }
            ),
            200,
            $headers
        );
    }

    /**
     * Return a single item
     *
     * @param Request $request
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function show(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        Validate::itemRoute($resource_type_id, $resource_id, $item_id);

        $item = (new Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            UtilityResponse::notFound(trans('entities.item'));
        }

        return response()->json(
            (new ItemTransformer($item))->toArray(),
            200,
            [
                'X-Total-Count' => 1
            ]
        );
    }

    /**
     * Generate the OPTIONS request for the item list
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $this->collection_parameters = Get::parameters(['year', 'month', 'category', 'sub_category']);

        $this->setConditionalGetParameters($resource_type_id);

        return $this->generateOptionsForIndex(
            [
                'description_localisation' => 'route-descriptions.item_GET_index',
                'parameters_config' => 'api.item.parameters.collection',
                'conditionals' => $this->get_parameters,
                'authenticated' => false
            ],
            [
                'description_localisation' => 'route-descriptions.item_POST',
                'fields_config' => 'api.item.fields',
                'conditionals' => [],
                'authenticated' => true
            ]
        );
    }

    /**
     * Generate the OPTIONS request for a specific item
     *
     * @param Request $request
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function optionsShow(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        Validate::itemRoute($resource_type_id, $resource_id, $item_id);

        $item = (new Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            UtilityResponse::notFound(trans('entities.item'));
        }

        return $this->generateOptionsForShow(
            [
                'description_key' => 'route-descriptions.item_GET_show',
                'parameters_key' => 'api.parameters-and-fields.item.parameters.item',
                'conditionals' => [],
                'authenticated' => false
            ],
            [
                'description_key' => 'route-descriptions.item_DELETE',
                'authenticated' => true
            ],
            [
                'description_key' => 'route-descriptions.item_PATCH',
                'fields_key' => 'api.parameters-and-fields.item.fields',
                'conditionals' => [],
                'authenticated' => false
            ]
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
        Validate::resourceRoute($resource_type_id, $resource_id);

        $validator = (new ItemValidator)->create($request);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $item = new Item([
                'resource_id' => $resource_id,
                'description' => $request->input('description'),
                'effective_date' => $request->input('effective_date'),
                'total' => $request->input('total'),
                'percentage' => $request->input('percentage', 100),
            ]);
            $item->setActualisedTotal($item->total, $item->percentage);
            $item->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ItemTransformer($item))->toArray(),
            201
        );
    }

    /**
     * Update the selected item
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function update(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        Validate::itemRoute($resource_type_id, $resource_id, $item_id);

        if ($this->isThereAnythingToPatchInRequest() === false) {
            UtilityResponse::nothingToPatch();
        }

        $validate = (new ItemValidator)->update($request);
        if ($validate->fails() === true) {
            return $this->returnValidationErrors($validate);
        }

        $invalid_fields = $this->areThereInvalidFieldsInRequest((new Item())->patchableFields());
        if ($invalid_fields !== false) {
            UtilityResponse::invalidFieldsInRequest($invalid_fields);
        }

        $item = (new Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            UtilityResponse::failedToSelectModelForUpdate();
        }

        $update_actualised = false;
        foreach ($request->all() as $key => $value) {
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
        Validate::resourceRoute($resource_type_id, $resource_id);

        $item = (new Item())->single($resource_type_id, $resource_id, $item_id);

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
     * Set any conditional GET parameters, will be merged with the data arrays defined in
     * config/api/route.php
     *
     * @param integer $resource_type_id
     *
     * @return void
     */
    private function setConditionalGetParameters($resource_type_id)
    {
        $this->get_parameters = [
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
            $this->get_parameters['year']['allowed_values'][$i] = [
                'value' => $i,
                'name' => $i,
                'description' => 'Include results for ' . $i
            ];
        }

        for ($i=1; $i < 13; $i++) {
            $this->get_parameters['month']['allowed_values'][$i] = [
                'value' => $i,
                'name' => date("F", mktime(0, 0, 0, $i, 10)),
                'description' => 'Include results for ' . date("F", mktime(0, 0, 0, $i, 1))
            ];
        }

        (new Category())->paginatedCollection($this->include_private, ['resource_type'=>$resource_type_id])->map(
            function ($category)
            {
                $this->get_parameters['category']['allowed_values'][$this->hash->encode('category', $category->category_id)] = [
                    'value' => $this->hash->encode('category', $category->category_id),
                    'name' => $category->category_name,
                    'description' => 'Include results for ' . $category->category_name . ' category'
                ];
            }
        );

        if (array_key_exists('category', $this->collection_parameters) === true) {
            (new SubCategory())->paginatedCollection($this->collection_parameters['category'])->map(
                function ($sub_category)
                {
                    $this->get_parameters['sub_category']['allowed_values'][$this->hash->encode('sub_category', $sub_category->id)] = [
                        'value' => $this->hash->encode('sub_category', $sub_category->id),
                        'name' => $sub_category->name,
                        'description' => 'Include results for ' . $sub_category->name . ' sub category'
                    ];
                }
            );
        }
    }
}
