<?php
declare(strict_types=1);

namespace App\Utilities;

use Illuminate\Http\JsonResponse;

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
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Response
{
    /**
     * Return not found, 404
     *
     * @param string|null $type Entity type that cannot be found
     *
     * @return JsonResponse
     */
    static public function notFound(?string $type = null): JsonResponse
    {
        response()->json(
            [
                'message' => ($type !== null) ? trans('responses.not-found-entity', ['type'=>$type]) :
                    trans('responses.not-found')
            ],
            404
        )->send();
        exit;
    }

    /**
     * Return not found, 404
     *
     * @param string|null $type Entity type that cannot be found
     *
     * @return JsonResponse
     */
    static public function notFoundOrNotAccessible(?string $type = null): JsonResponse
    {
        response()->json(
            [
                'message' => ($type !== null) ? trans('responses.not-found-or-not-accessible-entity', ['type'=>$type]) :
                    trans('responses.not-found')
            ],
            404
        )->send();
        exit;
    }

    /**
     * Return a foreign key constraint error, 500
     *
     * @param string $message Custom message for error
     *
     * @return JsonResponse
     */
    static public function foreignKeyConstraintError($message = ''): JsonResponse
    {
        response()->json(
            [
                'message' => (strlen($message) > 0) ? $message : trans('responses.constraint')
            ],
            500
        )->send();
        exit;
    }

    /**
     * 500 error, unable to select the data ready to update
     *
     * Until we add logging this is an unknown server error, later we will
     * add MySQL error logging
     *
     * @return JsonResponse
     */
    static public function failedToSelectModelForUpdate(): JsonResponse
    {
        response()->json(
            [
                'message' => trans('responses.model-select-failure'),
            ],
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
     * @return JsonResponse
     */
    static public function failedToSaveModelForUpdate(): JsonResponse
    {
        response()->json(
            [
                'message' => trans('responses.model-save-failure-update'),
            ],
            500
        )->send();
        exit();
    }

    /**
     * 403 error, authentication required
     *
     * @return JsonResponse
     */
    static public function authenticationRequired(): JsonResponse
    {
        response()->json(
            [
                'message' => trans('responses.authentication-required')
            ],
            403
        )->send();
        exit();
    }

    /**
     * 500 error, failed to save the model.
     *
     * Until we add logging this is an unknown server error, later we will
     * add MySQL error logging
     *
     * @return JsonResponse
     */
    static public function failedToSaveModelForCreate(): JsonResponse
    {
        response()->json(
            [
                'message' => trans('responses.model-save-failure-create'),
            ],
            500
        )->send();
        exit();
    }

    /**
     * 404 error, unable to decode the selected value, hasher missing or value
     * invalid
     *
     * @return JsonResponse
     */
    static public function unableToDecode(): JsonResponse
    {
        response()->json(
            [
                'message' => trans('responses.decode-error')
            ],
            500
        )->send();
        exit;
    }

    /**
     * 204, successful request, no content to return, typically a PATCH
     *
     * @return JsonResponse
     */
    static public function successNoContent(): JsonResponse
    {
        response()->json([],204)->send();
        exit;
    }

    /**
     * 200, successful request, no content to return
     *
     * @param boolean $array Return empty array, if false empty object
     * @return JsonResponse
     */
    static public function successEmptyContent(bool $array = false): JsonResponse
    {
        response()->json(($array === true ? [] : null),200)->send();
        exit;
    }

    /**
     * 400 error, nothing to PATCH, bad request
     *
     * @return JsonResponse
     */
    static public function nothingToPatch()
    {
        response()->json(
            [
                'message' => trans('responses.patch-empty')
            ],
            400
        )->send();
        exit();
    }

    /**
     * 400 error, invalid fields in the request, therefore bad request
     *
     * @param array $invalid_fields An array of invalid fields
     *
     * @return JsonResponse
     */
    static public function invalidFieldsInRequest(array $invalid_fields): JsonResponse
    {
        response()->json(
            [
                'message' => trans('responses.patch-invalid'),
                'fields' => $invalid_fields
            ],
            400
        )->send();
        exit();
    }

    /**
     * 503, maintenance
     *
     * @return JsonResponse
     */
    static public function maintenance(): JsonResponse
    {
        response()->json(
            [
                'message' => trans('responses.maintenance')
            ],
            503
        )->send();
        exit();
    }

    /**
     * 422 error, validation error
     *
     * @param array $validation_errors
     *
     * @return JsonResponse
     */
    static public function validationErrors(array $validation_errors): JsonResponse
    {
        response()->json(
            [
                'message' => trans('responses.validation'),
                'fields' => $validation_errors
            ],
            422
        )->send();
        exit();
    }
}
