<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

/**
 * Manage resource types
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeController extends Controller
{
    /**
     * Return all the resource types
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $headers = [
            'X-Total-Count' => 30,
        ];

        $link = $this->generateLinkHeader(10, 0, 20);
        if ($link !== null) {
            $headers['Link'] = $link;
        }

        return response()->json(
            [
                'results' => [
                    ['resource_type_id' => $this->hash->encode(1)],
                    ['resource_type_id' => $this->hash->encode(2)],
                    ['resource_type_id' => $this->hash->encode(3)]
                ]
            ],
            200,
            $headers
        );
    }

    /**
     * Return a single resource type
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $resource_type_id)
    {
        return response()->json(
            [
                'result' => [
                    'resource_type_id' => $resource_type_id
                ]
            ],
            200,
            [
                'X-Total-Count' => 1
            ]
        );
    }

    /**
     * Generate the OPTIONS request for the resource type list
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsIndex(Request $request)
    {
        return $this->generateOptionsForIndex(
            'descriptions.resource_type.GET_index',
            'descriptions.resource_type.POST',
            'routes.resource_type.fields'
        );
    }

    /**
     * Generate the OPTIONS request fir a specific resource type
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsShow(Request $request)
    {
        return $this->generateOptionsForShow(
            'descriptions.resource_type.GET_show',
            'descriptions.resource_type.DELETE',
            'descriptions.resource_type.PATCH',
            'routes.resource_type.fields'
        );
    }

    /**
     * Create a new resource type
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            Config::get('routes.resource_type.validation.POST')
        );

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        return response()->json(
            [
                'result' => [
                    'resource_type_id' => $this->hash->encode($new_resource_id = 4)
                ]
            ],
            200
        );
    }

    /**
     * Delete a resource type
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, string $resource_type_id)
    {
        return response()->json(null,204);
    }

    /**
     * Update the request resource type
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $resource_type_id)
    {
        $validator = Validator::make(
            $request->all(),
            Config::get('routes.resource_type.validation.PATCH')
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
                    'resource_type_id' => $resource_type_id
                ]
            ],
            200
        );
    }
}
