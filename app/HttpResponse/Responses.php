<?php
declare(strict_types=1);

namespace App\HttpResponse;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Responses
{
    private static function addException(array $response, ?Exception $e = null): array
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

    public static function notFound(?string $type = null, ?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => ($type !== null) ? trans('responses.not-found-entity', ['type'=>$type]) :
                trans('responses.not-found')
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            404
        );
    }

    public static function notFoundOrNotAccessible(?string $type = null, ?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => ($type !== null) ? trans('responses.not-found-or-not-accessible-entity', ['type'=>$type]) :
                trans('responses.not-found')
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            ($type !== null ? 404 : 403)
        );
    }

    public static function foreignKeyConstraintError($message = '', ?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => (strlen($message) > 0) ? $message : trans('responses.constraint')
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            409
        );
    }

    public static function failedToSelectModelForUpdateOrDelete(?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.model-select-failure'),
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            500
        );
    }

    public static function failedToSaveModelForUpdate(?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.model-save-failure-update'),
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            500
        );
    }

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

        return response()
            ->json($response,400);
    }

    public static function notSupported(?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.not-supported')
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        return response()
            ->json($response,405);
    }

    public static function failedToSaveModelForCreate(?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.model-save-failure-create'),
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            500
        );
    }

    public static function unableToDecode(?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.decode-error')
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            500
        );
    }

    public static function successNoContent(?Exception $e = null): JsonResponse
    {
        $response = [];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        return response()->json($response,204);
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

        return response()
            ->json($response,400);
    }

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

    public static function maintenance(?Exception $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.maintenance')
        ];

        if ($e instanceOf Exception) {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            503
        );
    }

    public static function validationErrors(
        Validator $validator,
        array $allowed_values = [],
        ?Exception $e = null
    ): ?JsonResponse {
        $validation_errors = [];

        foreach ($validator->errors()->toArray() as $field => $errors) {
            foreach ($errors as $error) {
                $validation_errors[$field]['errors'][] = $error;
            }
        }

        if (count($allowed_values) > 0) {
            $validation_errors = array_merge_recursive($validation_errors, $allowed_values);
        }

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
