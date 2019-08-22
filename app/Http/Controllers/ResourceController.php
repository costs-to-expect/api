<?php

namespace App\Http\Controllers;

use App\Option\Delete;
use App\Option\Get;
use App\Option\Post;
use App\Utilities\Pagination as UtilityPagination;
use App\Validators\Request\Route;
use App\Models\Resource;
use App\Models\Transformers\Resource as ResourceTransformer;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Request\Fields\Resource as ResourceValidator;
use App\Validators\Request\SearchParameters;
use App\Validators\Request\SortParameters;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage resources
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceController extends Controller
{
    protected $allow_entire_collection = true;

    /**
     * Return all the resources
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index(string $resource_type_id): JsonResponse
    {
        Route::resourceTypeRoute($resource_type_id);

        $search_parameters = SearchParameters::fetch(
            Config::get('api.resource.searchable')
        );

        $total = (new Resource())->totalCount(
            $resource_type_id,
            $this->include_private,
            $search_parameters
        );

        $sort_parameters = SortParameters::fetch(
            Config::get('api.resource.sortable')
        );

        $pagination = UtilityPagination::init(
                request()->path(),
                $total,
                10,
                $this->allow_entire_collection
            )->
            setSearchParameters($search_parameters)->
            setSortParameters($sort_parameters)->
            paging();

        $resources = (new Resource)->paginatedCollection(
            $resource_type_id,
            $pagination['offset'],
            $pagination['limit'],
            $search_parameters,
            $sort_parameters
        );

        $headers = [
            'X-Count' => count($resources),
            'X-Total-Count' => $total,
            'X-Offset' => $pagination['offset'],
            'X-Limit' => $pagination['limit'],
            'X-Link-Previous' => $pagination['links']['previous'],
            'X-Link-Next' => $pagination['links']['next']
        ];

        $sort_header = SortParameters::xHeader();
        if ($sort_header !== null) {
            $headers['X-Sort'] = $sort_header;
        }

        $search_header = SearchParameters::xHeader();
        if ($search_header !== null) {
            $headers['X-Search'] = $search_header;
        }

        return response()->json(
            array_map(
                function($resource) {
                    return (new ResourceTransformer($resource))->toArray();
                },
                $resources
            ),
            200,
            $headers
        );
    }

    /**
     * Return a single resource
     *
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function show(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        Route::resourceRoute($resource_type_id, $resource_id);

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
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(string $resource_type_id): JsonResponse
    {
        Route::resourceTypeRoute($resource_type_id);

        $get = Get::init()->
            setDescription('route-descriptions.resource_GET_index')->
            setSortable('api.resource.sortable')->
            setSearchable('api.resource.searchable')->
            setPaginationOverride(true)->
            setParameters('api.resource.parameters.collection')->
            option();

        $post = Post::init()->
            setDescription('route-descriptions.resource_POST')->
            setFields('api.resource.fields')->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $post,
            200
        );
    }

    /**
     * Generate the OPTIONS request for a specific category
     *
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function optionsShow(string $resource_type_id, string $resource_id): JsonResponse
    {
        Route::resourceRoute($resource_type_id, $resource_id);

        $resource = (new Resource)->single(
            $resource_type_id,
            $resource_id
        );

        if ($resource === null) {
            UtilityResponse::notFound(trans('entities.resource'));
        }

        $get = Get::init()->
            setDescription('route-descriptions.resource_GET_show')->
            setParameters('api.resource.parameters.item')->
            option();

        $delete = Delete::init()->
            setDescription('route-descriptions.resource_DELETE')->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $delete,
            200
        );
    }

    /**
     * Create a new resource
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function create(string $resource_type_id): JsonResponse
    {
        Route::resourceTypeRoute($resource_type_id);

        $validator = (new ResourceValidator)->create(['resource_type_id' => $resource_type_id]);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $resource = new Resource([
                'resource_type_id' => $resource_type_id,
                'name' => request()->input('name'),
                'description' => request()->input('description'),
                'effective_date' => request()->input('effective_date')
            ]);
            $resource->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ResourceTransformer((New Resource())->instanceToArray($resource)))->toArray(),
            201
        );
    }

    /**
     * Delete the requested resource
     *
     * @param string $resource_type_id,
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function delete(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        Route::resourceRoute($resource_type_id, $resource_id);

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
