<?php

namespace App\Http\Controllers;

use App\Option\Get;
use App\Validators\Request\Parameters;
use App\Models\RequestLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryRequestController extends Controller
{
    private $collection_parameters;

    /**
     * Return a summary of the access log, requests per year and month
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function accessLog(Request $request): JsonResponse
    {
        $this->collection_parameters = Parameters::fetch(['source']);

        $request_data = (new RequestLog())->monthlyRequests($this->collection_parameters);

        $summary = [];
        foreach ($request_data as $month) {
            $summary[$month['year']][] = ['month' => $month['month'], 'requests' => $month['requests']];
        }

        return response()->json(
            $summary,
            200
        );
    }

    /**
     * Generate the OPTIONS request for summary of the access log
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function optionsAccessLog(Request $request)
    {
        $get = Get::init()->
            setParameters('api.request-access-log.parameters.collection')->
            setDescription('route-descriptions.summary_GET_request_access-log')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            option();

        return $this->optionsResponse($get, 200);
    }
}
