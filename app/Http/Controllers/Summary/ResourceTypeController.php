<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\Models\Summary\ResourceType;
use App\Option\Get;
use App\Response\Header\Header;
use App\Request\Parameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Summary controller for the resource-type routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeController extends Controller
{
    /**
     * Return a summary of the resource types
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $search_parameters = Parameter\Search::fetch(
            array_keys(Config::get('api.resource-type.summary-searchable'))
        );

        $summary = (new ResourceType())->totalCount(
            $this->permitted_resource_types,
            $this->include_public,
            $search_parameters
        );

        $headers = new Header();
        $headers->add('X-Total-Count', $summary);
        $headers->add('X-Count', $summary);

        return response()->json(
            [
                'resource_types' => $summary
            ],
            200,
            $headers->headers()
        );
    }


    /**
     * Generate the OPTIONS request for the resource type summaries
     *
     * @return JsonResponse
     */
    public function optionsIndex(): JsonResponse
    {
        $get = Get::init()->
            setParameters('api.resource-type.summary-parameters')->
            setDescription('route-descriptions.summary-resource-type-GET-index')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            setSearchable('api.resource-type.summary-searchable')->
            option();

        return $this->optionsResponse($get, 200);
    }
}
