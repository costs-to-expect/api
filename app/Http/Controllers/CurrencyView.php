<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Option\CurrencyCollection;
use App\Option\CurrencyItem;
use App\Response\Cache;
use App\Response\Header\Headers;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Pagination as UtilityPagination;
use App\Models\Transformers\Currency as CurrencyTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class CurrencyView extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Return all the currency
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneYear();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_collection->valid() === false) {

            $search_parameters = Parameter\Search::fetch(
                Config::get('api.currency.searchable')
            );

            $sort_parameters = Parameter\Sort::fetch(
                Config::get('api.currency.sortable')
            );

            $total = (new Currency())->totalCount($search_parameters);

            $pagination = new UtilityPagination(request()->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                setSearchParameters($search_parameters)->
                setSortParameters($sort_parameters)->
                parameters();

            $currencies = (new Currency())->paginatedCollection(
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $search_parameters,
                $sort_parameters
            );

            $collection = array_map(
                static function($currency) {
                    return (new CurrencyTransformer($currency))->asArray();
                },
                $currencies
            );

            $headers = new Headers();
            $headers->collection($pagination_parameters, count($currencies), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addSearch(Parameter\Search::xHeader())->
                addSort(Parameter\Sort::xHeader());

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    /**
     * Return a single currency
     *
     * @param string $currency_id
     *
     * @return JsonResponse
     */
    public function show(string $currency_id): JsonResponse
    {
        Route\Validate::currency($currency_id);

        $currency = (new Currency())->single($currency_id);

        if ($currency === null) {
            return \App\Response\Responses::notFound(trans('entities.currency'));
        }

        $headers = new Headers();
        $headers->item();

        return response()->json(
            (new CurrencyTransformer($currency))->asArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the currencies collection
     *
     * @return JsonResponse
     */
    public function optionsIndex(): JsonResponse
    {
        $response = new CurrencyCollection(['view'=> $this->user_id !== null]);

        return $response->create()->response();
    }

    /**
     * Generate the OPTIONS request for a specific currency
     *
     * @param string $currency_id
     *
     * @return JsonResponse
     */
    public function optionsShow(string $currency_id): JsonResponse
    {
        Route\Validate::currency($currency_id);

        $response = new CurrencyItem(['view'=> $this->user_id !== null]);

        return $response->create()->response();
    }
}
