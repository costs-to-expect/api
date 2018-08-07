<?php

namespace App\Http\Controllers;

use App\Http\Route\Validators\ResourceType as ResourceTypeRouteValidator;
use App\Models\Resource;
use App\Models\ResourceType;
use App\Transformers\Resource as ResourceTransformer;
use App\Validators\Resource as ResourceValidator;
use Exception;
use Illuminate\Database\QueryException;
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
        if (ResourceTypeRouteValidator::validate($resource_type_id) === false) {
            return $this->returnResourceNotFound();
        }

        $resources = (new Resource)->paginatedCollection($resource_type_id);

        $headers = [
            'X-Total-Count' => count($resources)
        ];

        $link = $this->generateLinkHeader(10, 0, 20);
        if ($link !== null) {
            $headers['Link'] = $link;
        }

        return response()->json(
            $resources->map(
                function ($resource)
                {
                    return (new ResourceTransformer($resource))->toArray();
                }
            ),
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
    public function show(
        Request $request,
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        if (ResourceTypeRouteValidator::validate($resource_type_id) === false) {
            return $this->returnResourceNotFound();
        }

        $resource = (new Resource)->single($resource_type_id, $resource_id);

        if ($resource === null) {
            return $this->returnResourceNotFound();
        }

        return response()->json(
            (new ResourceTransformer($resource))->toArray(),
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
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(Request $request, string $resource_type_id): JsonResponse
    {
        if ((new ResourceType)->find($resource_type_id) === null) {
            return $this->returnResourceNotFound();
        }

        return $this->generateOptionsForIndex(
            'api.descriptions.resource.GET_index',
            'api.descriptions.resource.POST',
            'api.routes.resource.fields',
            'api.routes.resource.parameters'
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
        if (ResourceTypeRouteValidator::validate($resource_type_id) === false) {
            return $this->returnResourceNotFound();
        }

        $resource = (new Resource)->single(
            $resource_type_id,
            $resource_id
        );

        if ($resource === null) {
            return $this->returnResourceNotFound();
        }

        return $this->generateOptionsForShow(
            'api.descriptions.resource.GET_show',
            'api.descriptions.resource.DELETE',
            'api.descriptions.resource.PATCH',
            'api.routes.resource.fields'
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
        if (ResourceTypeRouteValidator::validate($resource_type_id) === false) {
            return $this->returnResourceNotFound();
        }

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
                    'message' => 'Error creating new record'
                ],
                500
            );
        }

        return response()->json(
            (new ResourceTransformer($resource))->toArray(),
            201
        );
    }

    /**
     * Delete the requested resource
     *
     * @param Request $request,
     * @param string $resource_type_id,
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function delete(
        Request $request,
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        if (ResourceTypeRouteValidator::validate($resource_type_id) === false) {
            return $this->returnResourceNotFound();
        }

        $resource = (new Resource())->single(
            $resource_type_id,
            $resource_id
        );

        if ($resource === null) {
            return $this->returnResourceNotFound();
        }

        try {
            $resource->delete();

            return response()->json([], 204);
        } catch (QueryException $e) {
            return $this->returnForeignKeyConstraintError();
        } catch (Exception $e) {
            return $this->returnResourceNotFound();
        }
    }
}
