<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Option\QueueCollection;
use App\Option\QueueItem;
use App\Response\Cache;
use App\Response\Header\Headers;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Pagination as UtilityPagination;
use App\Models\Transformers\Queue as QueueTransformer;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class QueueView extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlFivesMinutes();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_collection->valid() === false) {

            $total = (new Queue())->totalCount();

            $pagination = new UtilityPagination(request()->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                parameters();

            $jobs = (new Queue())->paginatedCollection(
                $pagination_parameters['offset'],
                $pagination_parameters['limit']
            );

            $collection = array_map(
                static function($jon) {
                    return (new QueueTransformer($jon))->asArray();
                },
                $jobs
            );

            $headers = new Headers();
            $headers->collection($pagination_parameters, count($jobs), $total)->
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
     * @return JsonResponse
     */
    public function show(string $queue_id): JsonResponse
    {
        Route\Validate::queue((int) $queue_id);

        $job = (new Queue())->single($queue_id);

        if ($job === null) {
            return \App\Response\Responses::notFound(trans('entities.queue'));
        }

        $headers = new Headers();
        $headers->item();

        return response()->json(
            (new QueueTransformer($job))->asArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * @return JsonResponse
     */
    public function optionsIndex(): JsonResponse
    {
        $response = new QueueCollection(['view'=> $this->user_id !== null]);

        return $response->create()->response();
    }

    /**
     * @param string $queue_id
     *
     * @return JsonResponse
     */
    public function optionsShow(string $queue_id): JsonResponse
    {
        Route\Validate::queue((int) $queue_id);

        $response = new QueueItem(['view'=> $this->user_id !== null]);

        return $response->create()->response();
    }
}
