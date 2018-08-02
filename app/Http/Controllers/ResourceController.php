<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Transformers\Resource as ResourceTransformer;
use App\Validators\Resource as ResourceValidator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * @return JsonResponse
     */
    public function index(Request $request, string $resource_type_id): JsonResponse
    {
        $resources = (new Resource)
            ->where('resource_type_id', '=', $resource_type_id)
            ->get();

        $headers = [
            'X-Total-Count' => count($resources)
        ];

        $link = $this->generateLinkHeader(10, 0, 20);
        if ($link !== null) {
            $headers['Link'] = $link;
        }

        return response()->json(
            [
                'results' => $resources->map(
                    function ($resource)
                    {
                        return (new ResourceTransformer($resource))->toArray();
                    }
                )
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
     * @return JsonResponse
     */
    public function show(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        $resource = (new Resource)
            ->where('resource_type_id', '=', $resource_type_id)
            ->find($resource_id);

        if ($resource === null) {
            return $this->returnResourceNotFound();
        }

        return response()->json(
            [
                'result' => (new ResourceTransformer($resource))->toArray()
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
     * @return JsonResponse
     */
    public function optionsIndex(Request $request): JsonResponse
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
     * @return JsonResponse
     */
    public function optionsShow(Request $request, string $resource_type_id, string $resource_id): JsonResponse
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
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function create(Request $request, string $resource_type_id): JsonResponse
    {
        $validator = (new ResourceValidator)->create($request, $resource_type_id);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $resource = new Resource([
                'resource_type_id' => $resource_type_id,
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'effective_date' => $request->input('effective_date')
            ]);
            $resource->save();
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
                'result' => (new ResourceTransformer($resource))->toArray()
            ],
            201
        );
    }

    /**
     * Delete a resource
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function delete(Request $request, string $resource_type_id, string $resource_id): JsonResponse
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
     * @return JsonResponse
     */
    public function update(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        $validator = (new ResourceValidator)->update($request, $resource_type_id, $resource_id);

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
