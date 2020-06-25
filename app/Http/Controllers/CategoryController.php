<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Option\Delete;
use App\Option\Get;
use App\Option\Patch;
use App\Option\Post;
Use App\Response\Cache;
use App\Response\Header\Header;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Header\Headers;
use App\Response\Pagination as UtilityPagination;
use App\Models\Category;
use App\Models\Transformers\Category as CategoryTransformer;
use App\Request\Validate\Category as CategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class CategoryController extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Return the categories collection
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index($resource_type_id): JsonResponse
    {
        Route\Validate::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneMonth();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_collection->valid() === false) {

            $search_parameters = Parameter\Search::fetch(
                array_keys(Config::get('api.category.searchable'))
            );

            $sort_parameters = Parameter\Sort::fetch(
                Config::get('api.category.sortable')
            );

            $total = (new Category())->total(
                (int)$resource_type_id,
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

            $categories = (new Category())->paginatedCollection(
                (int)$resource_type_id,
                $this->permitted_resource_types,
                $this->include_public,
                $pagination['offset'],
                $pagination['limit'],
                $search_parameters,
                $sort_parameters
            );

            $collection = array_map(
                static function ($category) {
                    return (new CategoryTransformer($category))->asArray();
                },
                $categories
            );

            $headers = new Headers();
            $headers->collection($pagination, count($categories), $total)->
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
     * Return a single category
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function show($resource_type_id, $category_id): JsonResponse
    {
        Route\Validate::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types
        );

        $parameters = Parameter\Request::fetch(array_keys(Config::get('api.category.parameters.item')));

        $category = (new Category)->single(
            (int) $resource_type_id,
            (int) $category_id
        );

        if ($category === null) {
            \App\Response\Responses::notFound(trans('entities.category'));
        }

        $subcategories = [];
        if (
            array_key_exists('include-subcategories', $parameters) === true &&
            $parameters['include-subcategories'] === true
        ) {
            $subcategories = (new Subcategory())->paginatedCollection(
                (int) $resource_type_id,
                (int) $category_id,
                0,
                100
            );
        }

        $headers = new Header();
        $headers->item();

        $parameters_header = Parameter\Request::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            (new CategoryTransformer($category, ['subcategories'=>$subcategories]))->asArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the category list
     *
     * @param $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex($resource_type_id): JsonResponse
    {
        Route\Validate::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setParameters('api.category.parameters.collection')->
            setSortable('api.category.sortable')->
            setSearchable('api.category.searchable')->
            setPaginationOverride(true)->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.category_GET_index')->
            option();

        $post = Post::init()->
            setFields('api.category.fields')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            setDescription('route-descriptions.category_POST')->
            option();

        return $this->optionsResponse(
            $get + $post,
            200
        );
    }

    /**
     * Generate the OPTIONS request for a specific category
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function optionsShow($resource_type_id, $category_id): JsonResponse
    {
        Route\Validate::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setParameters('api.category.parameters.item')->
            setDescription('route-descriptions.category_GET_show')->
            setAuthenticationStatus($permissions['view'])->
            option();

        $delete = Delete::init()->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            setDescription('route-descriptions.category_DELETE')->
            option();

        $patch = Patch::init()->
            setFields('api.category.fields-patch')->
            setDescription('route-descriptions.category_PATCH')->
            setAuthenticationStatus($permissions['manage'])->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $delete + $patch,
            200
        );
    }

    /**
     * Create a new category
     *
     * @param $resource_type_id
     *
     * @return JsonResponse
     */
    public function create($resource_type_id): JsonResponse
    {
        Route\Validate::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        $validator = (new CategoryValidator)->create([
            'resource_type_id' => $resource_type_id
        ]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        try {
            $category = new Category([
                'name' => request()->input('name'),
                'description' => request()->input('description'),
                'resource_type_id' => $resource_type_id
            ]);
            $category->save();

            $cache_control->clearPrivateCacheKeys([
                $cache_key->categories($resource_type_id)
            ]);

            if (in_array($resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->categories($resource_type_id)
                ]);
            }
        } catch (Exception $e) {
           \App\Response\Responses::failedToSaveModelForCreate();
        }

        return response()->json(
            (new CategoryTransformer((new Category)->instanceToArray($category)))->asArray(),
            201
        );
    }

    /**
     * Delete the requested category
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function delete(
        $resource_type_id,
        $category_id
    ): JsonResponse
    {
        Route\Validate::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        try {
            (new Category())->find($category_id)->delete();
            $cache_control->clearPrivateCacheKeys([
                $cache_key->categories($resource_type_id)
            ]);

            if (in_array($resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->categories($resource_type_id)
                ]);
            }

            \App\Response\Responses::successNoContent();
        } catch (QueryException $e) {
            \App\Response\Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            \App\Response\Responses::notFound(trans('entities.category'), $e);
        }
    }

    /**
     * Update the selected category
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function update($resource_type_id, $category_id): JsonResponse
    {
        Route\Validate::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        $category = (new Category())->instance($category_id);

        if ($category === null) {
            \App\Response\Responses::failedToSelectModelForUpdateOrDelete();
        }

        \App\Request\BodyValidation::checkForEmptyPatch();

        $validator = (new CategoryValidator)->update([
            'resource_type_id' => (int)$category->resource_type_id,
            'category_id' => (int)$category_id
        ]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        \App\Request\BodyValidation::checkForInvalidFields(
            array_merge(
                (new Category())->patchableFields(),
                (new CategoryValidator)->dynamicDefinedFields()
            )
        );

        foreach (request()->all() as $key => $value) {
            $category->$key = $value;
        }

        try {
            $category->save();

            $cache_control->clearPrivateCacheKeys([
                // We need to clear categories, resource type items
                // and items due to includes so simpler to clear the entire
                // resource type
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
