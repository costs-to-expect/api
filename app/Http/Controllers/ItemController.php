<?php

namespace App\Http\Controllers;

use App\Http\Route\Validators\Resource as ResourceRouteValidator;
use App\Models\Item;
use App\Transformers\Item as ItemTransformer;
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
    private $pagination = [];

    /**
     * Return all the items
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function index(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $total = Item::count();

        $this->pagination($request, $total);

        $items = (new Item())->paginatedCollection(
            $resource_type_id,
            $resource_id,
            $this->pagination['offset'],
            $this->pagination['limit']
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
        if (
            ResourceRouteValidator::validate($resource_type_id, $resource_id) === false ||
            $item_id === 'nill'
        ) {
            return $this->returnResourceNotFound();
        }

        $item = (new Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            return $this->returnResourceNotFound();
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
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        return $this->generateOptionsForIndex(
            'api.descriptions.item.GET_index',
            'api.descriptions.item.POST',
            'api.routes.item.fields',
            'api.routes.item.parameters'
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
        if (
            ResourceRouteValidator::validate($resource_type_id, $resource_id) === false ||
            $item_id === 'nill'
        ) {
            return $this->returnResourceNotFound();
        }

        $item = (new Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            return $this->returnResourceNotFound();
        }

        return $this->generateOptionsForShow(
            'api.descriptions.item.GET_show',
            'api.descriptions.item.DELETE',
            'api.descriptions.item.PATCH',
            'api.routes.item.fields'
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
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        };

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
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $item = (new Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            return $this->returnResourceNotFound();
        }

        try {
            $item->delete();

            return response()->json([], 204);
        } catch (QueryException $e) {
            return $this->returnForeignKeyConstraintError();
        } catch (Exception $e) {
            return $this->returnResourceNotFound();
        }
    }

    /**
     * Generate the pagination parameters
     *
     * @param Request $request
     * @param integer $total
     */
    private function pagination(Request $request, $total)
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

        $this->pagination['link'] = $this->generateLinkHeader(
            $this->pagination['limit'],
            $previous_offset,
            $next_offset
        );
    }
}
