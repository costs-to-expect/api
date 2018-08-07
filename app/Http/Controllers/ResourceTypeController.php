<?php

namespace App\Http\Controllers;

use App\Models\ResourceType;
use App\Transformers\ResourceType as ResourceTypeTransformer;
use App\Validators\ResourceType as ResourceTypeValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage resource types
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeController extends Controller
{
    private $parameters_index = [];

    /**
     * Return all the resource types
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $resource_types = (new ResourceType())->paginatedCollection();

        $this->parameters_index['include_resources'] = boolval($request->query('include_resources', false));

        $headers = [
            'X-Total-Count' => count($resource_types)
        ];

        return response()->json(
            $resource_types->map(
                function ($resource_type)
                {
                    return (new ResourceTypeTransformer($resource_type, $this->parameters_index))->toArray();
                }
            ),
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
     * @return JsonResponse
     */
    public function show(Request $request, string $resource_type_id): JsonResponse
    {
        $resource_type = (new ResourceType())->single($resource_type_id);

        if ($resource_type === null) {
            return $this->returnResourceNotFound();
        }

        return response()->json(
            (new ResourceTypeTransformer($resource_type))->toArray(),
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
     * @return JsonResponse
     */
    public function optionsIndex(Request $request): JsonResponse
    {
        return $this->generateOptionsForIndex(
            'api.descriptions.resource_type.GET_index',
            'api.descriptions.resource_type.POST',
            'api.routes.resource_type.fields',
            'api.routes.resource_type.parameters'
        );
    }

    /**
     * Generate the OPTIONS request fir a specific resource type
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsShow(Request $request, string $resource_type_id): JsonResponse
    {
        if ((new ResourceType)->single($resource_type_id) === null) {
            return $this->returnResourceNotFound();
        }

        return $this->generateOptionsForShow(
            'api.descriptions.resource_type.GET_show',
            'api.descriptions.resource_type.DELETE',
            'api.descriptions.resource_type.PATCH',
            'api.routes.resource_type.fields'
        );
    }

    /**
     * Create a new resource type
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = (new ResourceTypeValidator)->create($request);

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
                    'message' => 'Error creating new record'
                ],
                500
            );
        }

        return response()->json(
            (new ResourceTypeTransformer($resource_type))->toArray(),
            201
        );
    }

    /**
     * Delete the requested resource type
     *
     * @param Request $request,
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function delete(
        Request $request,
        string $resource_type_id
    ): JsonResponse
    {
        $resource_type = (new ResourceType())->single($resource_type_id);

        if ($resource_type === null) {
            return $this->returnResourceNotFound();
        }

        try {
            $resource_type->delete();

            return response()->json([], 204);
        } catch (QueryException $e) {
            return $this->returnForeignKeyConstraintError();
        } catch (Exception $e) {
            return $this->returnResourceNotFound();
        }
    }
}
