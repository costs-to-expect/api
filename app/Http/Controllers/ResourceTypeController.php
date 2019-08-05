<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Utilities\Pagination as UtilityPagination;
use App\Validators\Request\Parameters;
use App\Validators\Request\Route;
use App\Models\ResourceType;
use App\Models\Transformers\ResourceType as ResourceTypeTransformer;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Request\Fields\ResourceType as ResourceTypeValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

/**
 * Manage resource types
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeController extends Controller
{
    /**
     * Return all the resource types
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $total = (new ResourceType())->totalCount(
            $this->include_private
        );

        $pagination = UtilityPagination::init(request()->path(), $total)
            ->paging();

        $resource_types = (new ResourceType())->paginatedCollection(
            $this->include_private,
            $pagination['offset'],
            $pagination['limit']
        );

        $headers = [
            'X-Count' => count($resource_types),
            'X-Total-Count' => $total,
            'X-Offset' => $pagination['offset'],
            'X-Limit' => $pagination['limit'],
            'X-Link-Previous' => $pagination['links']['previous'],
            'X-Link-Next' => $pagination['links']['next']
        ];

        return response()->json(
            array_map(
                function($resource_type) {
                    return (new ResourceTypeTransformer($resource_type))->toArray();
                },
                $resource_types
            ),
            200,
            $headers
        );
    }

    /**
     * Return a single resource type
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function show(string $resource_type_id): JsonResponse
    {
        Route::resourceTypeRoute($resource_type_id);

        $parameters = Parameters::fetch(['include-resources']);

        $resource_type = (new ResourceType())->single(
            $resource_type_id,
            $this->include_private
        );

        if ($resource_type === null) {
            UtilityResponse::notFound(trans('entities.resource-type'));
        }

        $resources = [];
        if (
            array_key_exists('include-resources', $parameters) === true &&
            $parameters['include-resources'] === true
        ) {
            $resources = (new Resource())->paginatedCollection($resource_type_id);
        }

        return response()->json(
            (new ResourceTypeTransformer($resource_type, $resources))->toArray(),
            200,
            [
                'X-Total-Count' => 1
            ]
        );
    }

    /**
     * Generate the OPTIONS request for the resource type list
     *
     * @return JsonResponse
     */
    public function optionsIndex(): JsonResponse
    {
        return $this->generateOptionsForIndex(
            [
                'description_localisation_string' => 'route-descriptions.resource_type_GET_index',
                'parameters_config_string' => [],
                'conditionals_config' => [],
                'sortable_config' => null,
                'searchable_config' => null,
                'enable_pagination' => true,
                'authentication_required' => false
            ],
            [
                'description_localisation_string' => 'route-descriptions.resource_type_POST',
                'fields_config' => 'api.resource-type.fields',
                'conditionals_config' => [],
                'authentication_required' => true
            ]
        );
    }

    /**
     * Generate the OPTIONS request fir a specific resource type
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsShow(string $resource_type_id): JsonResponse
    {
        Route::resourceTypeRoute($resource_type_id);

        return $this->generateOptionsForShow(
            [
                'description_localisation_string' => 'route-descriptions.resource_type_GET_show',
                'parameters_config_string' => 'api.resource-type.parameters.item',
                'conditionals_config' => [],
                'authentication_required' => false
            ],
            [
                'description_localisation_string' => 'route-descriptions.resource_type_DELETE',
                'authentication_required' => true
            ]
        );
    }

    /**
     * Create a new resource type
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $validator = (new ResourceTypeValidator)->create();

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $resource_type = new ResourceType([
                'name' => request()->input('name'),
                'description' => request()->input('description'),
                'private' => request()->input('private', 0)
            ]);
            $resource_type->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ResourceTypeTransformer((New ResourceType())->instanceToArray($resource_type)))->toArray(),
            201
        );
    }

    /**
     * Delete the requested resource type
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function delete(
        string $resource_type_id
    ): JsonResponse
    {
        Route::resourceTypeRoute($resource_type_id);

        try {
            (new ResourceType())->find($resource_type_id)->delete();
            UtilityResponse::successNoContent();
        } catch (QueryException $e) {
            UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityResponse::notFound(trans('entities.resource-type'));
        }
    }
}
