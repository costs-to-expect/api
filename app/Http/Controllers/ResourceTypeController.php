<?php

namespace App\Http\Controllers;

use App\Models\PermittedUser;
use App\Models\Resource;
use App\Option\Delete;
use App\Option\Get;
use App\Option\Patch;
use App\Option\Post;
use App\Utilities\Pagination as UtilityPagination;
use App\Utilities\Request as UtilityRequest;
use App\Validators\Request\Parameters;
use App\Validators\Request\Route;
use App\Models\ResourceType;
use App\Models\Transformers\ResourceType as ResourceTypeTransformer;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Request\Fields\ResourceType as ResourceTypeValidator;
use App\Validators\Request\SearchParameters;
use App\Validators\Request\SortParameters;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * Manage resource types
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeController extends Controller
{
    protected $allow_entire_collection = true;

    /**
     * Return all the resource types
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $search_parameters = SearchParameters::fetch(
            Config::get('api.resource-type.searchable')
        );

        $total = (new ResourceType())->totalCount(
            $this->permitted_resource_types,
            $this->include_public,
            $search_parameters
        );

        $sort_parameters = SortParameters::fetch(
            Config::get('api.resource-type.sortable')
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

        $resource_types = (new ResourceType())->paginatedCollection(
            $this->permitted_resource_types,
            $this->include_public,
            $pagination['offset'],
            $pagination['limit'],
            $search_parameters,
            $sort_parameters
        );

        $headers = [
            'X-Count' => count($resource_types),
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
            $resources = (new Resource())->paginatedCollection(
                $resource_type_id
            );
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
        $get = Get::init()->
            setDescription('route-descriptions.resource_type_GET_index')->
            setSortable('api.resource-type.sortable')->
            setSearchable('api.resource-type.searchable')->
            setPaginationOverride(true)->
            option();

        $post = Post::init()->
            setDescription('route-descriptions.resource_type_POST')->
            setFields('api.resource-type.fields')->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $post,
            200
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

        $get = Get::init()->
            setDescription('route-descriptions.resource_type_GET_show')->
            setParameters('api.resource-type.parameters.item')->
            option();

        $delete = Delete::init()->
            setDescription('route-descriptions.resource_type_DELETE')->
            setAuthenticationRequired(true)->
            option();

        $patch = Patch::init()->
            setDescription('route-descriptions.resource_type_PATCH')->
            setFields('api.resource-type.fields')->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $delete + $patch,
            200
        );
    }

    /**
     * Create a new resource type
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $validator = (new ResourceTypeValidator)->create([
            'user_id' => Auth::user()->id
        ]);
        UtilityRequest::validateAndReturnErrors($validator);

        try {
            $resource_type = new ResourceType([
                'name' => request()->input('name'),
                'description' => request()->input('description'),
                'public' => request()->input('public', 0)
            ]);
            $resource_type->save();

            $permitted_users = new PermittedUser([
                'resource_type_id' => $resource_type->id,
                'user_id' => Auth::user()->id
            ]);
            $permitted_users->save();
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
            (new PermittedUser())->instance($resource_type_id, Auth::user()->id)->delete();
            (new ResourceType())->find($resource_type_id)->delete();
            UtilityResponse::successNoContent();
        } catch (QueryException $e) {
            UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityResponse::notFound(trans('entities.resource-type'));
        }
    }

    /**
     * Update the selected category
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function update(
        string $resource_type_id
    ): JsonResponse
    {
        Route::resourceTypeRoute($resource_type_id);

        $resource_type = (new ResourceType())->instance($resource_type_id);

        if ($resource_type === null) {
            UtilityResponse::failedToSelectModelForUpdate();
        }

        UtilityRequest::checkForEmptyPatch();

        $validator = (new ResourceTypeValidator())->update([
            'resource_type_id' => intval($resource_type_id),
            'user_id' => Auth::user()->id
        ]);
        UtilityRequest::validateAndReturnErrors($validator);

        UtilityRequest::checkForInvalidFields(
            array_merge(
                (new ResourceType())->patchableFields(),
                (new ResourceTypeValidator())->dynamicDefinedFields()
            )
        );

        foreach (request()->all() as $key => $value) {
            $resource_type->$key = $value;
        }

        try {
            $resource_type->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForUpdate();
        }

        UtilityResponse::successNoContent();
    }
}
