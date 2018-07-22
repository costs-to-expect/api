<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, string $resource_type_id, string $resource_id)
    {
        return response()->json(
            [
                'results' => [
                    ['item_id' => $this->hash->encode(1)],
                    ['item_id' => $this->hash->encode(2)],
                    ['item_id' => $this->hash->encode(3)]
                ]
            ],
            200
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $resource_type_id, string $resource_id, string $item_id)
    {
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

    /**
     * Generate the OPTIONS request for the item list
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsIndex(Request $request, string $resource_type_id, string $resource_id)
    {
        return $this->generateOptionsForIndex(
            'descriptions.item.GET_index',
            'descriptions.item.POST',
            'fields.item.fields'
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsShow(Request $request, string $resource_type_id, string $resource_id, string $item_id)
    {
        return $this->generateOptionsForShow(
            'descriptions.item.GET_show',
            'descriptions.item.DELETE',
            'descriptions.item.PATCH',
            'fields.item.fields'
        );
    }

    /**
     * Create a new item
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request, string $resource_type_id, string $resource_id)
    {
        $validator = Validator::make(
            $request->all(),
            Config::get('fields.resource.item.POST')
        );

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        return response()->json(
            [
                'result' => [
                    'item_id' => $this->hash->encode($new_item_id = 4)
                ]
            ],
            200
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, string $resource_type_id, string $resource_id, string $item_id)
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $resource_type_id, string $resource_id, string $item_id)
    {
        $validator = Validator::make(
            $request->all(),
            Config::get('fields.resource.item.PATCH')
        );

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
