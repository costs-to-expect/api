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
     * @param string $message Custom message for error
     */
    static public function notFound($message = '')
    {
        response()->json(
            [
                'message' => (strlen($message) > 0) ? $message : 'Resource not found'
            ],
            404
        )->send();
        exit;
    }

    /**
     * Return a foreign key constraint error, 500
     *
     * @param string $message Custom message for error
     */
    static public function foreignKeyConstraintError($message = '')
    {
        response()->json(
            [
                'message' => (strlen($message) > 0) ? $message : 'Unable to delete resource, dependant data exists'
            ],
            500
        )->send();
        exit;
    }

    /**
     * Return a conflict, 409
     *
     * @param string $message Custom message for error
     */
    static public function conflict($message = '')
    {
        response()->json(
            [
                'message' => (strlen($message) > 0) ? $message : 'Value already set, conflict'
            ],
            409
        )->send();
        exit;
    }
}
