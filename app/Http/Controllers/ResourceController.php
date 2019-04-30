<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Route\Validate;
use App\Models\Resource;
use App\Models\Transformers\Resource as ResourceTransformer;
use App\Utilities\Response as UtilityResponse;
use App\Http\Parameters\Request\Validators\Resource as ResourceValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage resources
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
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
        Validate::resourceTypeRoute($resource_type_id);

        $resources = (new Resource)->paginatedCollection($resource_type_id);

        $headers = [
            'X-Total-Count' => count($resources)
        ];

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
        Validate::resourceRoute($resource_type_id, $resource_id);

        $resource = (new Resource)->single($resource_type_id, $resource_id);

        if ($resource === null) {
            UtilityResponse::notFound(trans('entities.resource'));
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
        Validate::resourceTypeRoute($resource_type_id);

        return $this->generateOptionsForIndex(
            [
                'description_localisation' => 'route-descriptions.resource_GET_index',
                'parameters_config' => 'api.resource.parameters.collection',
                'conditionals' => [],
                'sortable_config' => null,
                'pagination' => false,
                'authenticated' => false
            ],
            [
                'description_localisation' => 'route-descriptions.resource_POST',
                'fields_config' => 'api.resource.fields',
                'conditionals' => [],
                'authenticated' => true
            ]
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
        Validate::resourceRoute($resource_type_id, $resource_id);

        $resource = (new Resource)->single(
            $resource_type_id,
            $resource_id
        );

        if ($resource === null) {
            UtilityResponse::notFound(trans('entities.resource'));
        }

        return $this->generateOptionsForShow(
            [
                'description_localisation' => 'route-descriptions.resource_GET_show',
                'parameters_config' => 'api.resource.parameters.item',
                'conditionals' => [],
                'authenticated' => false
            ],
            [
                'description_localisation' => 'route-descriptions.resource_DELETE',
                'authenticated' => true
            ]
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
        Validate::resourceTypeRoute($resource_type_id);

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
            UtilityResponse::failedToSaveModelForCreate();
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
        Validate::resourceRoute($resource_type_id, $resource_id);

        try {
            (new Resource())->find($resource_id)->delete();

            UtilityResponse::successNoContent();
        } catch (QueryException $e) {
            UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityResponse::notFound(trans('entities.resource'));
        }
    }
}
