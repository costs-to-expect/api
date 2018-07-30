<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Transformers\Item as ItemTransformer;
use App\Validators\Item as ItemValidator;
use Exception;
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
        $resource_type_id = $this->decodeParameter($resource_type_id);
        $resource_id = $this->decodeParameter($resource_id);

        $items = (new Item())
            ->where('resource_id', '=', $resource_id)
            ->get();

        $headers = [
            'X-Total-Count' => count($items)
        ];

        $link = $this->generateLinkHeader(10, 0, 20);
        if ($link !== null) {
            $headers['Link'] = $link;
        }

        return response()->json(
            [
                'results' => $items->map(
                    function ($item)
                    {
                        return (new ItemTransformer($item))->toArray();
                    }
                )
            ],
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
        $resource_type_id = $this->decodeParameter($resource_type_id);
        $resource_id = $this->decodeParameter($resource_id);
        $item_id = $this->decodeParameter($item_id);

        $item = (new Item())
            ->where('resource_id', '=', $resource_id)
            ->find($item_id);

        if ($item === null) {
            return $this->returnResourceNotFound();
        }

        return response()->json(
            [
                'result' => (new ItemTransformer($item))->toArray()
            ],
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
        return $this->generateOptionsForIndex(
            'descriptions.item.GET_index',
            'descriptions.item.POST',
            'routes.item.fields',
            'routes.item.parameters'
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
        return $this->generateOptionsForShow(
            'descriptions.item.GET_show',
            'descriptions.item.DELETE',
            'descriptions.item.PATCH',
            'routes.item.fields'
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
        $resource_type_id = $this->decodeParameter($resource_type_id);
        $resource_id = $this->decodeParameter($resource_id);

        $validator = ItemValidator::create($request, $resource_type_id, $resource_id);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $item = new Item([
                'resource_id' => $resource_id,
                'description' => $request->input('description'),
                'effective_date' => $request->input('effective_date'),
                'total' => $request->input('total'),
                'percentage' => $request->input('percentage'),
            ]);
            $item->setActualisedTotal($item->total, $item->percentage);
            $item->save();
        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => 'Error creating new record'
                ],
                500
            );
        }

        return response()->json(
            [
                'result' => (new ItemTransformer($item))->toArray()
            ],
            201
        );
    }

    /**
     * Delete an item
     *
     * @param Request $request
     * @param string $resource_id
     * @param string $resource_type_id
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
        return response()->json(null, 204);
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
        $resource_type_id = $this->decodeParameter($resource_type_id);
        $resource_id = $this->decodeParameter($resource_id);
        $item_id = $this->decodeParameter($item_id);

        $validator = ItemValidator::update($request, $resource_type_id, $resource_id, $item_id);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        if (count($request->all()) === 0) {
            return $this->requireAtLeastOneFieldToPatch();
        }

        return response()->json(
            [
                'result' => [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id,
                    'item_id' => $item_id
                ]
            ],
            200
        );
    }
}
