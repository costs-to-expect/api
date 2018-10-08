<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Get;
use App\Http\Parameters\Route\Validate;
use App\Models\Category;
use App\Models\Item;
use App\Models\SubCategory;
use App\Transformers\Item as ItemTransformer;
use App\Utilities\Pagination as UtilityPagination;
use App\Utilities\Request as UtilityRequest;
use App\Validators\Item as ItemValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
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
        Validate::resource($resource_type_id, $resource_id);

        $this->collection_parameters = Get::parameters(['year', 'month', 'category', 'sub_category']);

        $total = (new Item())->totalCount(
            $resource_type_id,
            $resource_id,
            $this->collection_parameters
        );

        $this->pagination(
            $request,
            $total,
            $request->path(),
            $this->collection_parameters
        );

        $items = (new Item())->paginatedCollection(
            $resource_type_id,
            $resource_id,
            $this->pagination['offset'],
            $this->pagination['limit'],
            $this->collection_parameters
        );

        $headers = [
            'X-Count' => count($items),
            'X-Total-Count' => $total
        ];
        if ($this->pagination['link'] !== null) {
            $headers['Link'] = $this->pagination['link'];
        }

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
        Validate::resource($resource_type_id, $resource_id);

        $item = (new Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            return UtilityRequest::notFound();
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
        Validate::resource($resource_type_id, $resource_id);

        $this->collection_parameters = Get::parameters(['year', 'month', 'category', 'sub_category']);

        $this->setConditionalGetParameters();

        return $this->generateOptionsForIndex(
            'api.descriptions.item.GET_index',
            'api.descriptions.item.POST',
            'api.routes.item.fields',
            'api.routes.item.parameters.collection',
            [],
            $this->get_parameters
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
        Validate::resource($resource_type_id, $resource_id);

        $item = (new Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            return UtilityRequest::notFound();
        }

        return $this->generateOptionsForShow(
            'api.descriptions.item.GET_show',
            'api.descriptions.item.DELETE',
            'api.descriptions.item.PATCH',
            'api.routes.item.fields',
            'api.routes.item.parameters.item'
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
        Validate::resource($resource_type_id, $resource_id);

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
            return response()->json(
                [
                    'message' => 'Error creating new record'
                ],
                500
            );
        }

        return response()->json(
            (new ItemTransformer($item))->toArray(),
            201
        );
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
        Validate::resource($resource_type_id, $resource_id);

        $item = (new Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            return UtilityRequest::notFound();
        }

        try {
            $item->delete();

            return response()->json([], 204);
        } catch (QueryException $e) {
            return UtilityRequest::foreignKeyConstraintError();
        } catch (Exception $e) {
            return UtilityRequest::notFound();
        }
    }

    /**
     * Generate the pagination parameters
     *
     * @param Request $request
     * @param integer $total
     * @param string $uri
     * @param array $parameters_collection
     */
    private function pagination(
        Request $request,
        int $total,
        string $uri = '',
        array $parameters_collection = []
    )
    {
        $offset = intval($request->query('offset', 0));
        $limit = intval($request->query('limit', 10));

        $previous_offset = null;
        $next_offset = null;

        if ($offset !== 0) {
            $previous_offset = abs($offset - $limit);
        }
        if ($offset + $limit < $total) { $next_offset = $offset + $limit; }

        $this->pagination['offset'] = $offset;
        $this->pagination['limit'] = $limit;

        $parameters = '';
        foreach ($parameters_collection as $parameter => $parameter_value) {
            if ($parameter_value !== null) {
                if (strlen($parameters) > 0) {
                    $parameters .= '&';
                }

                switch ($parameter) {
                    case 'category':
                    case 'sub_category':
                        $parameters .= $parameter . '=' . $this->hash->encode($parameter, $parameter_value);
                        break;

                    default:
                        $parameters .= $parameter . '=' . $parameter_value;
                        break;
                }
            }
        }

        $this->pagination['link'] = UtilityPagination::headerLink(
            $uri,
            $parameters,
            $this->pagination['limit'],
            $previous_offset,
            $next_offset
        );
    }

    /**
     * Set any conditional GET parameters, will be merged with the data arrays defined in
     * config/api/route.php
     *
     * @return void
     */
    private function setConditionalGetParameters()
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

        (new Category())->paginatedCollection()->map(
            function ($category)
            {
                $this->get_parameters['category']['allowed_values'][$this->hash->encode('category', $category->id)] = [
                    'value' => $this->hash->encode('category', $category->id),
                    'name' => $category->name,
                    'description' => 'Include results for ' . $category->name . ' category'
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
