<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Manage resource types
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(
            [
                'results' => [
                    ['resource_type_id' => $this->hash->encode(1)],
                    ['resource_type_id' => $this->hash->encode(2)],
                    ['resource_type_id' => $this->hash->encode(3)]
                ]
            ],
            200
        );
    }

    /**
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $resource_type_id)
    {
        return response()->json(
            [
                'result' => [
                    'resource_type_id' => $resource_type_id
                ]
            ],
            200
        );
    }
}
