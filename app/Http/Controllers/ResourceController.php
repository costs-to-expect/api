<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

/**
 * Manage resources
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceController extends Controller
{
    /**
     * Return all the resources
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, string $resource_type_id)
    {
        $headers = [
            'X-Total-Count' => 30
        ];

        $link = $this->generateLinkHeader(10, 0, 20);
        if ($link !== null) {
            $headers['Link'] = $link;
        }

        return response()->json(
            [
                'results' => [
                    ['resource_id' => $this->hash->encode(1)],
                    ['resource_id' => $this->hash->encode(2)],
                    ['resource_id' => $this->hash->encode(3)]
                ]
            ],
            200,
            $headers
        );
    }

    /**
     * Return a single resource
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $resource_type_id, string $resource_id)
    {
        return response()->json(
            [
                'result' => [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id
                ]
            ],
            200,
            [
                'X-Total-Count' => 1
            ]
        );
    }

    /**
     * Generate the OPTIONS request for the resource list
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsIndex(Request $request)
    {
        return $this->generateOptionsForIndex(
            'descriptions.resource.GET_index',
            'descriptions.resource.POST',
            'routes.resource.fields',
            'routes.resource.parameters'
        );
    }

    /**
     * Generate the OPTIONS request for a specific category
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsShow(Request $request, string $resource_type_id, string $resource_id)
    {
        return $this->generateOptionsForShow(
            'descriptions.resource.GET_show',
            'descriptions.resource.DELETE',
            'descriptions.resource.PATCH',
            'routes.resource.fields'
        );
    }

    /**
     * Create a new resource
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request, string $resource_type_id)
    {
        $validator = Validator::make(
            $request->all(),
            Config::get('routes.resource.validation.POST')
        );

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        return response()->json(
            [
                'result' => [
                    'resource_id' => $this->hash->encode($new_resource_id = 4)
                ]
            ],
            200
        );
    }

    /**
     * Delete a resource
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, string $resource_type_id, string $resource_id)
    {
        return response()->json(null,204);
    }

    /**
     * Update the request resource
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $resource_type_id, string $resource_id)
    {
        $validator = Validator::make(
            $request->all(),
            Config::get('routes.resource.validation.PATCH')
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
                    'resource_id' => $resource_id
                ]
            ],
            200
        );
    }
}
