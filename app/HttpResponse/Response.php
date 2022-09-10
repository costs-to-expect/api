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
class Response
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

        if ($e instanceof Throwable && app()->environment() !== 'production') {
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

        if ($e instanceof Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            ($type !== null ? 403 : 404)
        );
    }

    public static function foreignKeyConstraintError(?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.constraint')
        ];

        if ($e instanceof Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            409
        );
    }

    public static function foreignKeyConstraintCategory(?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('constraint-category')
        ];

        if ($e instanceof Throwable && app()->environment() !== 'production') {
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

        if ($e instanceof Throwable && app()->environment() !== 'production') {
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

        if ($e instanceof Throwable && app()->environment() !== 'production') {
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

        if ($e instanceof Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            403
        );
    }

    public static function authenticationFailed(?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('auth.failed')
        ];

        if ($e instanceof Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            401
        );
    }

    public static function categoryAssignmentLimit(int $limit): JsonResponse
    {
        $response = [
            'message' => trans('responses.category-limit'),
            'limit' => $limit
        ];

        return response()->json($response, 400);
    }

    public static function notSupported(): JsonResponse
    {
        return response()
            ->json(
                [
                    'message' => trans('responses.not-supported')
                ],
                405
            );
    }

    public static function failedToSaveModelForCreate(?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('responses.model-save-failure-create'),
        ];

        if ($e instanceof Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            500
        );
    }

    public static function unableToCreateAccount(?Throwable $e = null): JsonResponse
    {
        $response = [
            'message' => trans('auth.unable-to-create-account')
        ];

        if ($e instanceof Throwable && app()->environment() !== 'production') {
            $response = self::addException($response, $e);
        }

        return response()->json(
            $response,
            500
        );
    }

    public static function unableToDecode(): JsonResponse
    {
        return response()->json(
            [
                'message' => trans('responses.decode-error')
            ],
            500
        );
    }

    public static function successNoContent(): JsonResponse
    {
        return response()->json([], 204);
    }

    public static function subcategoryAssignmentLimit(int $limit): JsonResponse
    {
        $response = [
            'message' => trans('responses.subcategory-limit'),
            'limit' => $limit
        ];

        return response()
            ->json($response, 400);
    }

    public static function nothingToPatch(): JsonResponse
    {
        return response()->json(
            [
                'message' => trans('responses.patch-empty')
            ],
            400
        );
    }

    public static function invalidFieldsInRequest(array $invalid_fields): JsonResponse
    {
        return response()->json(
            [
                'message' => trans('responses.patch-invalid'),
                'fields' => $invalid_fields
            ],
            400
        );
    }

    public static function maintenance(): JsonResponse
    {
        return response()->json(
            [
                'message' => trans('responses.maintenance')
            ],
            503
        );
    }

    public static function validationErrors(Validator $validator, array $allowed_values = []): JsonResponse
    {
        $validation_errors = [];

        foreach ($validator->errors()->toArray() as $field => $errors) {
            foreach ($errors as $error) {
                $validation_errors[$field]['errors'][] = $error;
            }
        }

        if (count($allowed_values) > 0) {
            $validation_errors = array_merge_recursive($validation_errors, $allowed_values);
        }

        return response()->json(
            [
                'message' => trans('responses.validation'),
                'fields' => $validation_errors
            ],
            422
        );
    }
}
