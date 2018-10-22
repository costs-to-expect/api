<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Get;
use App\Http\Parameters\Route\Validate;
use App\Models\ResourceType;
use App\Transformers\ResourceType as ResourceTypeTransformer;
use App\Utilities\Request as UtilityRequest;
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
    private $collection_parameters = [];
    private $show_parameters = [];

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

        $this->collection_parameters = Get::parameters(['include_resources']);

        $headers = [
            'X-Total-Count' => count($resource_types)
        ];

        return response()->json(
            $resource_types->map(
                function ($resource_type)
                {
                    return (new ResourceTypeTransformer($resource_type, $this->collection_parameters))->toArray();
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
        Validate::resourceType($resource_type_id);

        $this->show_parameters = Get::parameters(['include_resources']);

        $resource_type = (new ResourceType())->single($resource_type_id);

        if ($resource_type === null) {
            UtilityRequest::notFound();
        }

        return response()->json(
            (new ResourceTypeTransformer($resource_type, $this->show_parameters))->toArray(),
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
            'api.routes.resource_type.parameters.collection',
            'api.descriptions.resource_type.POST',
            'api.routes.resource_type.fields'
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
        Validate::resourceType($resource_type_id);

        return $this->generateOptionsForShow(
            'api.descriptions.resource_type.GET_show',
            'api.routes.resource_type.parameters.item',
            'api.descriptions.resource_type.DELETE'
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
        Validate::resourceType($resource_type_id);

        try {
            (new ResourceType())->find($resource_type_id)->delete();

            return response()->json([], 204);
        } catch (QueryException $e) {
            UtilityRequest::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityRequest::notFound();
        }
    }
}
