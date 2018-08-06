<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Resource;
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
        $items = (new Item())
            ->where('resource_id', '=', $resource_id)
            ->whereHas('resource', function ($query) use ($resource_type_id) {
                $query->where('resource_type_id', '=', $resource_type_id);
            })
            ->get();

        $headers = [
            'X-Total-Count' => count($items)
        ];

        $link = $this->generateLinkHeader(10, 0, 20);
        if ($link !== null) {
            $headers['Link'] = $link;
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
    public function show(Request $request, string $resource_type_id, string $resource_id, string $item_id): JsonResponse
    {
        $item = (new Item())
            ->where('resource_id', '=', $resource_id)
            ->whereHas('resource', function ($query) use ($resource_type_id) {
                $query->where('resource_type_id', '=', $resource_type_id);
            })
            ->find($item_id);

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
        if ($this->validateRoutesIds($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        if ($this->resourceValid($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        };

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
        $item = (new Item())
            ->where('resource_id', '=', $resource_id)
            ->whereHas('resource', function ($query) use ($resource_type_id) {
                $query->where('resource_type_id', '=', $resource_type_id);
            })
            ->find($item_id);

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
        if ($this->validateRoutesIds($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        if ($this->resourceValid($resource_type_id, $resource_id) === false) {
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

        $item = (new Item())
            ->where('resource_id', '=', $resource_id)
            ->whereHas('resource', function ($query) use ($resource_type_id) {
                $query->where('resource_type_id', '=', $resource_type_id);
            })
            ->find($item_id);

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
     * Update the request item
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
        $validator = (new ItemValidator)->update($request);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        if (count($request->all()) === 0) {
            return $this->requireAtLeastOneFieldToPatch();
        }

        return response()->json(
            [
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id,
                'item_id' => $item_id
            ],
            200
        );
    }

    /**
     * Check to see if the resource is valid, if not return a 404 as the ids
     * are invalid.
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     *
     * @return boolean
     */
    private function resourceValid(int $resource_type_id, int $resource_id): bool
    {
        $resource = (new Resource())
            ->where('resource_type_id', '=', $resource_type_id)
            ->find($resource_id);

        if ($resource === null) {
            return false;
        }

        return true;
    }

    /**
     * Check to see if the route ids are valid, should have been converted by
     * the middleware and not be strings
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     *
     * @return boolean
     */
    private function validateRoutesIds($resource_type_id, $resource_id): bool
    {
        if ($resource_type_id === 'nill' || $resource_id === 'nill') {
            return false;
        }

        return true;
    }
}
