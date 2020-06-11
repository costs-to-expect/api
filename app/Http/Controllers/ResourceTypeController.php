<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ItemType;
use App\Models\PermittedUser;
use App\Models\Resource;
use App\Models\ResourceTypeItemType;
use App\Option\Delete;
use App\Option\Get;
use App\Option\Patch;
use App\Option\Post;
use App\Response\Cache;
use App\Response\Header\Headers;
use App\Request\Parameter;
use App\Request\Route;
use App\Utilities\Pagination as UtilityPagination;
use App\Models\ResourceType;
use App\Models\Transformers\ResourceType as ResourceTypeTransformer;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Fields\ResourceType as ResourceTypeValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * Manage resource types
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeController extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Return all the resource types
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneDay();

        $search_parameters = Parameter\Search::fetch(
            array_keys(Config::get('api.resource-type.searchable'))
        );

        $sort_parameters = Parameter\Sort::fetch(
            Config::get('api.resource-type.sortable')
        );

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_collection->valid() === false) {
            $total = (new ResourceType())->totalCount(
                $this->permitted_resource_types,
                $this->include_public,
                $search_parameters
            );

            $pagination = UtilityPagination::init(
                request()->path(),
                $total,
                10,
                $this->allow_entire_collection
            )->setSearchParameters($search_parameters)->setSortParameters($sort_parameters)->paging();

            $resource_types = (new ResourceType())->paginatedCollection(
                $this->permitted_resource_types,
                $this->include_public,
                $pagination['offset'],
                $pagination['limit'],
                $search_parameters,
                $sort_parameters
            );

            $collection = array_map(
                static function ($resource_type) {
                    return (new ResourceTypeTransformer($resource_type))->toArray();
                },
                $resource_types
            );

            $headers = new Headers();
            $headers->collection($pagination, count($resource_types), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addSearch(Parameter\Search::xHeader())->
                addSort(Parameter\Sort::xHeader());

            $cache_collection->create($total, $collection, $pagination, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
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
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $parameters = Parameter\Request::fetch(array_keys(Config::get('api.resource-type.parameters.item')));

        $resource_type = (new ResourceType())->single(
            $resource_type_id,
            $this->permitted_resource_types,
            $this->include_public
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

        $headers = new Headers();
        $headers->item()->addParameters(Parameter\Request::xHeader());

        return response()->json(
            (new ResourceTypeTransformer($resource_type, $resources))->toArray(),
            200,
            $headers->headers()
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
            setSortable('api.resource-type.sortable')->
            setSearchable('api.resource-type.searchable')->
            setPaginationOverride(true)->
            setDescription('route-descriptions.resource_type_GET_index')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            option();

        $post = Post::init()->
            setFields('api.resource-type.fields')->
            setFieldsData($this->fieldsData())->
            setDescription('route-descriptions.resource_type_POST')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
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
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setParameters('api.resource-type.parameters.item')->
            setDescription('route-descriptions.resource_type_GET_show')->
            setAuthenticationStatus($permissions['view'])->
            option();

        $delete = Delete::init()->
            setDescription('route-descriptions.resource_type_DELETE')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            option();

        $patch = Patch::init()->
            setFields('api.resource-type.fields-patch')->
            setDescription('route-descriptions.resource_type_PATCH')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
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
        $user_id = Auth::user()->id;

        $validator = (new ResourceTypeValidator)->create([
            'user_id' => $user_id
        ]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        try {
            $resource_type = new ResourceType([
                'name' => request()->input('name'),
                'description' => request()->input('description'),
                'public' => request()->input('public', 0)
            ]);
            $resource_type->save();

            $permitted_users = new PermittedUser([
                'resource_type_id' => $resource_type->id,
                'user_id' => $user_id,
                'added_by' => $user_id
            ]);
            $permitted_users->save();

            $item_type_id = $this->hash->decode('item_type', request()->input('item_type_id'));

            if ($item_type_id === false) {
                UtilityResponse::unableToDecode();
            }

            $resource_type_item_type = new ResourceTypeItemType([
                'resource_type_id' => $resource_type->id,
                'item_type_id' => $item_type_id
            ]);
            $resource_type_item_type->save();
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
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types,
            true
        );

        $resource_type_item_type = (new ResourceTypeItemType())->instance($resource_type_id);
        $permitted_user = (new PermittedUser())->instance($resource_type_id, Auth::user()->id);
        $resource_type = (new ResourceType())->find($resource_type_id);

        $categories = (new Category())->total(
            $resource_type_id,
            $this->permitted_resource_types,
            $this->include_public
        );

        $resources = (new Resource())->totalCount(
            $resource_type_id,
            $this->permitted_resource_types,
            $this->include_public
        );

        if (
            $categories === 0 &&
            $resources === 0 &&
            $resource_type_item_type !== null &&
            $permitted_user !== null &&
            $resource_type !== null
        ) {
            try {
                $resource_type_item_type->delete();
                $permitted_user->delete();
                $resource_type->delete();
                UtilityResponse::successNoContent();
            } catch (QueryException $e) {
                UtilityResponse::foreignKeyConstraintError();
            } catch (Exception $e) {
                UtilityResponse::notFound(trans('entities.resource-type'), $e);
            }
        } else {
            UtilityResponse::foreignKeyConstraintError();
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
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types,
            true
        );

        $resource_type = (new ResourceType())->instance($resource_type_id);

        if ($resource_type === null) {
            UtilityResponse::failedToSelectModelForUpdateOrDelete();
        }

        \App\Request\BodyValidation::checkForEmptyPatch();

        $validator = (new ResourceTypeValidator())->update([
            'resource_type_id' => intval($resource_type_id),
            'user_id' => Auth::user()->id
        ]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        \App\Request\BodyValidation::checkForInvalidFields(
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

    /**
     * Generate any conditional POST parameters, will be merged with the relevant
     * config/api/[type]/fields.php data array
     *
     * @return array
     */
    private function fieldsData(): array
    {
        $item_types = (new ItemType())->minimisedCollection();

        $parameters = ['item_type_id' => []];
        foreach ($item_types as $item_type) {
            $id = $this->hash->encode('item_type', $item_type['item_type_id']);

            if ($id === false) {
                UtilityResponse::unableToDecode();
            }

            $parameters['item_type_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $item_type['item_type_name'],
                'description' => $item_type['item_type_description']
            ];
        }

        return $parameters;
    }
}
