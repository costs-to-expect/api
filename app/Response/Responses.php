<?php
declare(strict_types=1);

namespace App\Response;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

/**
 * Utility class to return default responses, we want some consistency
 * through out the API so all non expected responses should be returned via this
 * class
 *
 * As with all utility classes, eventually they may be moved into libraries if
 * they gain more than a few functions and the creation of a library makes
 * sense.
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Responses
{
    protected static function addException(array $response, ?Exception $e = null): array
    {
        if ($e !== null && App::environment() !== 'production') {
            $response['exception'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ];
        }

        return $response;
    }

    /**
     * Return not found, 404
     *
     * @param string|null $type Entity type that cannot be found
     * @param Exception|null $e
     *
     * @return JsonResponse
     */
    public static function notFound(?string $type = null, ?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => ($type !== null) ? trans('responses.not-found-entity', ['type'=>$type]) :
                trans('responses.not-found')
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        response()->json(
            $response,
            404
        )->send();
        exit;
    }

    /**
     * Return not found, 404
     *
     * @param string|null $type Entity type that cannot be found
     * @param Exception|null $e
     *
     * @return JsonResponse
     */
    public static function notFoundOrNotAccessible(?string $type = null, ?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => ($type !== null) ? trans('responses.not-found-or-not-accessible-entity', ['type'=>$type]) :
                trans('responses.not-found')
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        response()->json(
            $response,
            404
        )->send();
        exit;
    }

    /**
     * Return a foreign key constraint error, 500
     *
     * @param string $message Custom message for error
     * @param Exception|null $e
     *
     * @return JsonResponse
     */
    public static function foreignKeyConstraintError($message = '', ?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => (strlen($message) > 0) ? $message : trans('responses.constraint')
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        response()->json(
            $response,
            409
        )->send();
        exit;
    }

    /**
     * 500 error, unable to select the data ready to enable us to update or delete
     *
     * Until we add logging this is an unknown server error, later we will
     * add MySQL error logging
     *
     * @param Exception|null $e
     *
     * @return JsonResponse
     */
    public static function failedToSelectModelForUpdateOrDelete(?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.model-select-failure'),
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        response()->json(
            $response,
            500
        )->send();
        exit();
    }

    /**
     * 500 error, failed to save the model.
     *
     * Until we add logging this is an unknown server error, later we will
     * add MySQL error logging
     *
     * @param Exception|null $e
     *
     * @return JsonResponse
     */
    public static function failedToSaveModelForUpdate(?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.model-save-failure-update'),
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        response()->json(
            $response,
            500
        )->send();
        exit();
    }

    /**
     * 403 error, authentication required
     *
     * @param Exception|null $e
     *
     * @return JsonResponse
     */
    public static function authenticationRequired(?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.authentication-required')
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            403
        );
    }

    public static function categoryAssignmentLimit(int $limit, ?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.category-limit'),
            'limit' => $limit
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        response()
            ->json($response,400)
            ->send();
        exit();
    }

    public static function notSupported(?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.not-supported')
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        response()
            ->json($response,405)
            ->send();
        exit();
    }

    /**
     * 500 error, failed to save the model.
     *
     * Until we add logging this is an unknown server error, later we will
     * add MySQL error logging
     *
     * @param Exception|null $e
     *
     * @return JsonResponse
     */
    public static function failedToSaveModelForCreate(?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.model-save-failure-create'),
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        response()->json(
            $response,
            500
        )->send();
        exit();
    }

    /**
     * 404 error, unable to decode the selected value, hasher missing or value
     * invalid
     *
     * @param Exception|null $e
     *
     * @return JsonResponse
     */
    public static function unableToDecode(?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.decode-error')
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        response()->json(
            $response,
            500
        )->send();
        exit;
    }

    /**
     * 204, successful request, no content to return, typically a PATCH
     *
     * @param Exception|null $e
     *
     * @return JsonResponse
     */
    public static function successNoContent(?Exception $e = null): JsonResponse
    {
        $response = [];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        response()->json($response,204)->send();
        exit;
    }

    public static function subcategoryAssignmentLimit(int $limit, ?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.subcategory-limit'),
            'limit' => $limit
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        response()
            ->json($response,400)
            ->send();
        exit();
    }

    /**
     * 200, successful request, no content to return
     *
     * @param boolean $array Return empty array, if false empty object
     * @param Exception|null $e
     *
     * @return JsonResponse
     */
    public static function successEmptyContent(bool $array = false, ?Exception $e = null): JsonResponse
    {
        $response = ($array === true ? [] : null);

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        response()->json($response,200)->send();
        exit;
    }

    /**
     * 400 error, nothing to PATCH, bad request
     *
     * @param Exception|null $e
     *
     * @return JsonResponse
     */
    public static function nothingToPatch(?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.patch-empty')
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            400
        );
    }

    /**
     * 400 error, invalid fields in the request, therefore bad request
     *
     * @param array $invalid_fields An array of invalid fields
     * @param Exception|null $e
     *
     * @return JsonResponse
     */
    public static function invalidFieldsInRequest(array $invalid_fields, ?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.patch-invalid'),
            'fields' => $invalid_fields
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            400
        );
    }

    /**
     * 503, maintenance
     *
     * @param Exception|null $e
     *
     * @return JsonResponse
     */
    public static function maintenance(?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.maintenance')
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        response()->json(
            $response,
            503
        )->send();
        exit();
    }

    /**
     * 422 error, validation error
     *
     * @param array $validation_errors
     * @param Exception|null $e
     *
     * @return JsonResponse
     */
    public static function validationErrors(array $validation_errors, ?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.validation'),
            'fields' => $validation_errors
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            422
        );
    }
}
