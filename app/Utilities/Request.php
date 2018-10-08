<?php

namespace App\Utilities;

/**
 * Utility class to return default error responses
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Request
{
    /**
     * Return not found, 404
     *
     * @return \Illuminate\Http\JsonResponse
     */
    static public function notFound()
    {
        return response()->json(
            [
                'message' => 'Resource not found'
            ],
            404
        );
    }

    /**
     * Return a foreign key constraint error, 500
     *
     * @return \Illuminate\Http\JsonResponse
     */
    static public function foreignKeyConstraintError()
    {
        return response()->json(
            [
                'message' => 'Unable to delete resource, dependant data exists'
            ],
            500
        );
    }

    /**
     * Return a conflict, 409
     *
     * @return \Illuminate\Http\JsonResponse
     */
    static public function conflict()
    {
        return response()->json(
            [
                'message' => 'Value already set, conflict'
            ],
            409
        );
    }
}
