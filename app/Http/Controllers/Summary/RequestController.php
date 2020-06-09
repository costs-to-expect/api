<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\Option\Get;
use App\Response\Header\Header;
use App\Validators\Parameters;
use App\Models\Summary\RequestLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestController extends Controller
{
    private $collection_parameters;

    /**
     * Return a summary of the access log, requests per year and month
     *
     * @return JsonResponse
     */
    public function accessLog(): JsonResponse
    {
        $this->collection_parameters = Parameters::fetch(array_keys(Config::get('api.request-access-log.summary-parameters')));

        $request_data = (new RequestLog())->monthlyRequests($this->collection_parameters);

        $summary = [];
        foreach ($request_data as $month) {
            $summary[$month['year']][] = ['month' => $month['month'], 'requests' => $month['requests']];
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            $summary,
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for summary of the access log
     *
     * @return JsonResponse
     */
    public function optionsAccessLog()
    {
        $get = Get::init()->
            setParameters('api.request-access-log.parameters.collection')->
            setDescription('route-descriptions.summary_GET_request_access-log')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            option();

        return $this->optionsResponse($get, 200);
    }
}
