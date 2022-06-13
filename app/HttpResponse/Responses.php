<?php
declare(strict_types=1);

namespace App\HttpResponse;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Throwable;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Responses
{
    private static function addException(array $response, Throwable $e = null): array
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

    public static function notFound(?string $type = null, ?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => ($type !== null) ? trans('responses.not-found-entity', ['type'=>$type]) :
                trans('responses.not-found')
        ];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            404
        );
    }

    public static function notFoundOrNotAccessible(?string $type = null, ?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => ($type !== null) ? trans('responses.not-found-or-not-accessible-entity', ['type'=>$type]) :
                trans('responses.not-found')
        ];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            ($type !== null ? 403 : 404)
        );
    }

    public static function foreignKeyConstraintError($message = '', ?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => ($message !== '') ? $message : trans('responses.constraint')
        ];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            409
        );
    }

    public static function failedToSelectModelForUpdateOrDelete(?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.model-select-failure'),
        ];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            500
        );
    }

    public static function failedToSaveModelForUpdate(?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.model-save-failure-update'),
        ];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            500
        );
    }

    public static function authenticationRequired(?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.authentication-required')
        ];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            403
        );
    }

    public static function categoryAssignmentLimit(int $limit, ?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.category-limit'),
            'limit' => $limit
        ];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()
            ->json($response,400);
    }

    public static function notSupported(?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.not-supported')
        ];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()
            ->json($response,405);
    }

    public static function failedToSaveModelForCreate(?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.model-save-failure-create'),
        ];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            500
        );
    }

    public static function unableToDecode(?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.decode-error')
        ];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            500
        );
    }

    public static function successNoContent(?Throwable $e = null): JsonResponse
    {
        $response = [];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json($response,204);
    }

    public static function subcategoryAssignmentLimit(int $limit, ?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.subcategory-limit'),
            'limit' => $limit
        ];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()
            ->json($response,400);
    }

    public static function nothingToPatch(?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.patch-empty')
        ];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            400
        );
    }

    public static function invalidFieldsInRequest(array $invalid_fields, ?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.patch-invalid'),
            'fields' => $invalid_fields
        ];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            400
        );
    }

    public static function maintenance(?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.maintenance')
        ];

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
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
        ?Throwable $e = null
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

        if ($e instanceOf Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            422
        );
    }
}
