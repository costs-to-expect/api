<?php

namespace App\Http\Controllers;

use App\Models\ResourceType;
use App\Transformers\ResourceType as ResourceTypeTransformer;
use Exception;
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
        $resource_types = ResourceType::all();

        $headers = [
            'X-Total-Count' => count($resource_types)
        ];

        $link = $this->generateLinkHeader(10, 0, 20);
        if ($link !== null) {
            $headers['Link'] = $link;
        }

        return response()->json(
            [
                'results' => $resource_types->map(
                    function ($resource_type)
                    {
                        return (new ResourceTypeTransformer($resource_type))->toArray();
                    }
                )
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
        $resource_type_id = $this->decodeParameter($resource_type_id);

        $resource_type = ResourceType::find($resource_type_id);

        return response()->json(
            [
                'result' => (new ResourceTypeTransformer($resource_type))->toArray()
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
            'routes.resource_type.fields',
            'routes.resource_type.parameters'
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

        try {
            $resource_type = new ResourceType([
                'name' => $request->input('name'),
                'description' => $request->input('description')
            ]);
            $resource_type->save();
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
                'result' => (new ResourceTypeTransformer($resource_type))->toArray()
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
