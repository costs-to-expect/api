<?php

namespace App\Http\Controllers;

use App\Option\Delete;
use App\Option\Get;
use App\Option\Post;
use App\Response\Cache;
use App\Response\Header\Header;
use App\Request\Route;
use App\Models\Category;
use App\Models\ItemCategory;
use App\Models\Transformers\ItemCategory as ItemCategoryTransformer;
use Illuminate\Http\JsonResponse;

/**
 * Manage the category for an item row
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategoryView extends Controller
{
    /**
     * Return the category assigned to an item
     *
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function index(string $resource_type_id, string $resource_id, string $item_id): JsonResponse
    {
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneWeek();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_collection->valid() === false) {

            $item_category = (new ItemCategory())->paginatedCollection(
                $resource_type_id,
                $resource_id,
                $item_id
            );

            if ($item_category === null || (is_array($item_category) === true && count($item_category) === 0)) {
                $collection = [];
            } else {
                $collection = [(new ItemCategoryTransformer($item_category[0]))->asArray()];
            }

            $headers = new Header();
            $headers->add('X-Total-Count', 1);
            $headers->add('X-Count', 1);
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl());

            $cache_collection->create(count($collection), $collection, [], $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    /**
     * Return a single item
     *
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function show(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        if ($item_category_id === 'nill') {
            \App\Response\Responses::notFound(trans('entities.item-category'));
        }

        $item_category = (new ItemCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_category === null) {
            \App\Response\Responses::notFound(trans('entities.item-category'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new ItemCategoryTransformer($item_category))->asArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the item list
     *
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(string $resource_type_id, string $resource_id, string $item_id): JsonResponse
    {
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setParameters('api.item-category.parameters.collection')->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.item_category_GET_index')->
            option();

        $post = Post::init()->
            setFields('api.item-category.fields')->
            setFieldsData($this->fieldsData($resource_type_id))->
            setDescription('route-descriptions.item_category_POST')->
            setAuthenticationStatus($permissions['manage'])->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $post,
            200
        );
    }

    /**
     * Generate the OPTIONS request for a specific item
     *
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function optionsShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        if ($item_category_id === 'nill') {
            \App\Response\Responses::notFound(trans('entities.item-category'));
        }

        $item_category = (new ItemCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_category === null) {
            \App\Response\Responses::notFound(trans('entities.item-category'));
        }

        $get = Get::init()->
            setParameters('api.item-category.parameters.item')->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.item_category_GET_show')->
            option();

        $delete = Delete::init()->
            setDescription('route-descriptions.item_category_DELETE')->
            setAuthenticationStatus($permissions['manage'])->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $delete,
            200
        );
    }

    /**
     * Generate any conditional POST parameters, will be merged with the relevant
     * config/api/[type]/fields.php data array
     *
     * @param integer $resource_type_id
     *
     * @return array
     */
    private function fieldsData($resource_type_id): array
    {
        $categories = (new Category())->categoriesByResourceType($resource_type_id);

        $conditional_post_parameters = ['category_id' => []];
        foreach ($categories as $category) {
            $id = $this->hash->encode('category', $category['category_id']);

            if ($id === false) {
                \App\Response\Responses::unableToDecode();
            }

            $conditional_post_parameters['category_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $category['category_name'],
                'description' => $category['category_description']
            ];
        }

        return $conditional_post_parameters;
    }
}
