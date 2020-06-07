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
use App\Utilities\Header;
use App\Utilities\Pagination as UtilityPagination;
use App\Utilities\Request as UtilityRequest;
use App\Utilities\RoutePermission;
use App\Validators\Parameters;
use App\Validators\Route;
use App\Models\ResourceType;
use App\Models\Transformers\ResourceType as ResourceTypeTransformer;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Fields\ResourceType as ResourceTypeValidator;
use App\Validators\SearchParameters;
use App\Validators\SortParameters;
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
        $search_parameters = SearchParameters::fetch(
            array_keys(Config::get('api.resource-type.searchable'))
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

        $headers = new Header();
        $headers->collection($pagination, count($resource_types), $total);

        $sort_header = SortParameters::xHeader();
        if ($sort_header !== null) {
            $headers->addSort($sort_header);
        }

        $search_header = SearchParameters::xHeader();
        if ($search_header !== null) {
            $headers->addSearch($search_header);
        }

        return response()->json(
            array_map(
                function($resource_type) {
                    return (new ResourceTypeTransformer($resource_type))->toArray();
                },
                $resource_types
            ),
            200,
            $headers->headers()
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
        Route::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $parameters = Parameters::fetch(array_keys(Config::get('api.resource-type.parameters.item')));

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

        $headers = new Header();
        $headers->item();

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

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
        Route::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = RoutePermission::resourceType(
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
        Route::resourceType(
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
        Route::resourceType(
            $resource_type_id,
            $this->permitted_resource_types,
            true
        );

        $resource_type = (new ResourceType())->instance($resource_type_id);

        if ($resource_type === null) {
            UtilityResponse::failedToSelectModelForUpdateOrDelete();
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
