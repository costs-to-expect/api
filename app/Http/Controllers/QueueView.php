<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\HttpResponse\Header;
use App\Models\Queue;
use App\HttpOptionResponse\QueueCollection;
use App\HttpOptionResponse\QueueItem;
use App\HttpRequest\Parameter;
use App\Models\Permission;
use App\Transformer\Queue as QueueTransformer;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class QueueView extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $cache_control = new \App\Cache\Control();
        $cache_control->setTtlFivesMinutes();

        $cache_collection = new \App\Cache\Collection();
        $cache_collection->setFromCache($cache_control->getByKey($request->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {
            $total = (new Queue())->totalCount();

            $pagination = new \App\HttpResponse\Pagination($request->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                parameters();

            $jobs = (new Queue())->paginatedCollection(
                $pagination_parameters['offset'],
                $pagination_parameters['limit']
            );

            $collection = array_map(
                static function ($jon) {
                    return (new QueueTransformer($jon))->asArray();
                },
                $jobs
            );

            $headers = new Header();
            $headers->collection($pagination_parameters, count($jobs), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addSearch(Parameter\Search::xHeader())->
                addSort(Parameter\Sort::xHeader());

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->putByKey($request->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    /**
     * @return JsonResponse
     */
    public function show(string $queue_id): JsonResponse
    {
        if ((new Permission())->queueItemExists((int) $queue_id) === false) {
            return \App\HttpResponse\Response::notFound(trans('entities.queue'));
        }

        $job = (new Queue())->single($queue_id);

        if ($job === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.queue'));
        }

        $headers = new Header();
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
        if ((new Permission())->queueItemExists((int) $queue_id) === false) {
            return \App\HttpResponse\Response::notFound(trans('entities.queue'));
        }

        $response = new QueueItem(['view'=> $this->user_id !== null]);

        return $response->create()->response();
    }
}
