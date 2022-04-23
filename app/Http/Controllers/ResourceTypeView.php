<?php

namespace App\Http\Controllers;

use App\HttpOptionResponse\ResourceTypeCollection;
use App\HttpOptionResponse\ResourceTypeItem;
use App\HttpRequest\Parameter;
use App\HttpResponse\Header;
use App\HttpResponse\Responses;
use App\Models\AllowedValue\ItemType;
use App\Models\PermittedUser;
use App\Models\Resource;
use App\Models\ResourceType;
use App\Transformer\ResourceType as ResourceTypeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeView extends Controller
{
    protected bool $allow_entire_collection = true;

    public function index(Request $request): JsonResponse
    {
        $cache_control = new \App\Cache\Control( true, $this->user_id);
        $cache_control->setTtlOneWeek();

        $cache_collection = new \App\Cache\Collection();
        $cache_collection->setFromCache($cache_control->getByKey($request->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {

            $search_parameters = Parameter\Search::fetch(
                Config::get('api.resource-type.searchable')
            );

            $sort_parameters = Parameter\Sort::fetch(
                Config::get('api.resource-type.sortable')
            );

            $total = (new ResourceType())->totalCount(
                $this->viewable_resource_types,
                $search_parameters
            );

            $pagination = new \App\HttpResponse\Pagination($request->path(), $total);
            $pagination_parameters = $pagination
                ->allowPaginationOverride($this->allow_entire_collection)
                ->setSearchParameters($search_parameters)
                ->setSortParameters($sort_parameters)
                ->parameters();

            $resource_types = (new ResourceType())->paginatedCollection(
                $this->viewable_resource_types,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $search_parameters,
                $sort_parameters
            );

            $last_updated = null;
            if (count($resource_types) && array_key_exists('last_updated', $resource_types[0])) {
                $last_updated = $resource_types[0]['last_updated'];
            }

            $collection = array_map(
                static function ($resource_type) {
                    return (new ResourceTypeTransformer($resource_type))->asArray();
                },
                $resource_types
            );

            $headers = new Header();
            $headers
                ->collection($pagination_parameters, count($resource_types), $total)
                ->addCacheControl($cache_control->visibility(), $cache_control->ttl())
                ->addETag($collection)
                ->addSearch(Parameter\Search::xHeader())
                ->addSort(Parameter\Sort::xHeader());

            if ($last_updated !== null) {
                $headers->addLastUpdated($last_updated);
            }

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->putByKey($request->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    public function show($resource_type_id): JsonResponse
    {
        $parameters = Parameter\Request::fetch(array_keys(Config::get('api.resource-type.parameters-show')));

        $resource_type = (new ResourceType())->single(
            (int) $resource_type_id,
            $this->viewable_resource_types
        );

        if ($resource_type === null) {
            return Responses::notFound(trans('entities.resource-type'));
        }

        $transformer_relations = [];

        if (
            array_key_exists('include-resources', $parameters) === true &&
            $parameters['include-resources'] === true
        ) {
            $transformer_relations['resources'] = (new Resource())->paginatedCollection((int) $resource_type_id);
        }

        if (
            array_key_exists('include-permitted-users', $parameters) === true &&
            $parameters['include-permitted-users'] === true
        ) {
            $transformer_relations['permitted_users'] = (new PermittedUser())->paginatedCollection((int) $resource_type_id);
        }

        $headers = new Header();
        $headers->item()->addParameters(Parameter\Request::xHeader());

        return response()->json(
            (new ResourceTypeTransformer($resource_type, $transformer_relations))->asArray(),
            200,
            $headers->headers()
        );
    }

    public function optionsIndex(): JsonResponse
    {
        $response = new ResourceTypeCollection(['view'=> $this->user_id !== null, 'manage'=> $this->user_id !== null]);

        return $response->setAllowedValuesForFields((new ItemType())->allowedValues())
            ->create()
            ->response();
    }

    public function optionsShow($resource_type_id): JsonResponse
    {
        $response = new ResourceTypeItem($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }
}
