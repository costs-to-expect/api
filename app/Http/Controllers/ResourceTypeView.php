<?php

namespace App\Http\Controllers;

use App\AllowedValue\ItemType;
use App\Models\Resource;
use App\Models\ResourceType;
use App\Option\ResourceTypeCollection;
use App\Option\ResourceTypeItem;
use App\Request\Parameter;
use App\Response\Header;
use App\Response\Pagination as UtilityPagination;
use App\Transformers\ResourceType as ResourceTypeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage resource types
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeView extends Controller
{
    protected bool $allow_entire_collection = true;

    public function index(): JsonResponse
    {
        $cache_control = new \App\Cache\Control( true, $this->user_id);
        $cache_control->setTtlOneWeek();

        $cache_collection = new \App\Cache\Collection();
        $cache_collection->setFromCache($cache_control->getByKey(request()->getRequestUri()));

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

            $pagination = new UtilityPagination(request()->path(), $total);
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
            $cache_control->putByKey(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    public function show($resource_type_id): JsonResponse
    {
        $parameters = Parameter\Request::fetch(array_keys(Config::get('api.resource-type.parameters.item')));

        $resource_type = (new ResourceType())->single(
            $resource_type_id,
            $this->viewable_resource_types
        );

        if ($resource_type === null) {
            return \App\Response\Responses::notFound(trans('entities.resource-type'));
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
        $headers->item()->addParameters(Parameter\Request::xHeader());

        return response()->json(
            (new ResourceTypeTransformer($resource_type, ['resources' => $resources]))->asArray(),
            200,
            $headers->headers()
        );
    }

    public function optionsIndex(): JsonResponse
    {
        $response = new ResourceTypeCollection(['view'=> $this->user_id !== null, 'manage'=> $this->user_id !== null]);

        return $response->setAllowedValues((new ItemType())->allowedValues())
            ->create()
            ->response();
    }

    public function optionsShow($resource_type_id): JsonResponse
    {
        $response = new ResourceTypeItem($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }
}
