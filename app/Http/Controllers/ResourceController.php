<?php

namespace App\Http\Controllers;

use App\Option\Delete;
use App\Option\Get;
use App\Option\Patch;
use App\Option\Post;
use App\Response\Cache;
use App\Response\Header\Header;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Header\Headers;
use App\Utilities\Pagination as UtilityPagination;
use App\Models\Resource;
use App\Models\Transformers\Resource as ResourceTransformer;
use App\Request\Validate\Resource as ResourceValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * Manage resources
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceController extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Return all the resources
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index(string $resource_type_id): JsonResponse
    {
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneWeek();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_collection->valid() === false) {

            $search_parameters = Parameter\Search::fetch(
                array_keys(Config::get('api.resource.searchable'))
            );

            $sort_parameters = Parameter\Sort::fetch(
                Config::get('api.resource.sortable')
            );

            $total = (new Resource())->totalCount(
                $resource_type_id,
                $this->permitted_resource_types,
                $this->include_public,
                $search_parameters
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

            $collection = array_map(
                static function ($resource) {
                    return (new ResourceTransformer($resource))->asArray();
                },
                $resources
            );

            $headers = new Headers();
            $headers->collection($pagination, count($resources), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addSearch(Parameter\Search::xHeader())->
                addSort(Parameter\Sort::xHeader());

            $cache_collection->create($total, $collection, $pagination, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
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
        Route\Validate::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types
        );

        $resource = (new Resource)->single($resource_type_id, $resource_id);

        if ($resource === null) {
            \App\Response\Responses::notFound(trans('entities.resource'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new ResourceTransformer($resource))->asArray(),
            200,
            $headers->headers()
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
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setSortable('api.resource.sortable')->
            setSearchable('api.resource.searchable')->
            setPaginationOverride(true)->
            setParameters('api.resource.parameters.collection')->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.resource_GET_index')->
            option();

        $post = Post::init()->
            setFields('api.resource.fields')->
            setDescription('route-descriptions.resource_POST')->
            setAuthenticationStatus($permissions['manage'])->
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
        Route\Validate::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types
        );

        $resource = (new Resource)->single(
            $resource_type_id,
            $resource_id
        );

        if ($resource === null) {
            \App\Response\Responses::notFound(trans('entities.resource'));
        }

        $get = Get::init()->
            setParameters('api.resource.parameters.item')->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.resource_GET_show')->
            option();

        $delete = Delete::init()->
            setDescription('route-descriptions.resource_DELETE')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            option();

        $patch = Patch::init()->
            setFields('api.resource.fields')->
            setDescription('route-descriptions.resource_PATCH')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            option();

        return $this->optionsResponse(
            $get + $delete + $patch,
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
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        $validator = (new ResourceValidator)->create(['resource_type_id' => $resource_type_id]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        try {
            $resource = new Resource([
                'resource_type_id' => $resource_type_id,
                'name' => request()->input('name'),
                'description' => request()->input('description'),
                'effective_date' => request()->input('effective_date')
            ]);
            $resource->save();

            $cache_control->clearPrivateCacheKeys([
                $cache_key->resourceType($resource_type_id)
            ]);

            if (in_array($resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->resourceType($resource_type_id)
                ]);
            }
        } catch (Exception $e) {
            \App\Response\Responses::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ResourceTransformer((New Resource())->instanceToArray($resource)))->asArray(),
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
        Route\Validate::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        try {
            (new Resource())->find($resource_id)->delete();

            $cache_control->clearPrivateCacheKeys([
                $cache_key->resourceType($resource_type_id)
            ]);

            if (in_array($resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->resourceType($resource_type_id)
                ]);
            }

            \App\Response\Responses::successNoContent();
        } catch (QueryException $e) {
            \App\Response\Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            \App\Response\Responses::notFound(trans('entities.resource'), $e);
        }
    }

    /**
     * Update the selected resource
     *
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function update(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        Route\Validate::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        $resource = (new Resource())->instance($resource_type_id, $resource_id);

        if ($resource === null) {
            \App\Response\Responses::failedToSelectModelForUpdateOrDelete();
        }

        \App\Request\BodyValidation::checkForEmptyPatch();

        $validator = (new ResourceValidator())->update([
            'resource_type_id' => (int)$resource_type_id,
            'resource_id' => (int)$resource_id
        ]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        \App\Request\BodyValidation::checkForInvalidFields(
            array_merge(
                (new Resource())->patchableFields(),
                (new ResourceValidator())->dynamicDefinedFields()
            )
        );

        foreach (request()->all() as $key => $value) {
            $resource->$key = $value;
        }

        try {
            $resource->save();

            $cache_control->clearPrivateCacheKeys([
                $cache_key->resourceType($resource_type_id)
            ]);

            if (in_array($resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->resourceType($resource_type_id)
                ]);
            }
        } catch (Exception $e) {
            \App\Response\Responses::failedToSaveModelForUpdate();
        }

        \App\Response\Responses::successNoContent();
    }
}
