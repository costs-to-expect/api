<?php
declare(strict_types=1);

namespace App\Utilities;

use App\Utilities\Response as UtilityResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;

/**
 * Request utility class, helper methods for PATCH and POST methods
 *
 * As with all utility classes, eventually they may be moved into libraries if
 * they gain more than a few functions and the creation of a library makes
 * sense.
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Request
{
    /**
     * Check to see if any of the provided fields are invalid, we throw an error
     * if there are any invalid fields in the request, simpler to fail hard than
     * simply ignore invalid values
     *
     * @param array $patchable_fields
     *
     * @return JsonResponse|null
     */
    public static function checkForInvalidFields(array $patchable_fields): ?JsonResponse
    {
        $invalid_fields = [];
        foreach (request()->all() as $key => $value) {
            if (in_array($key, $patchable_fields) === false) {
                $invalid_fields[] = $key;
            }
        }

        if (count($invalid_fields) !== 0) {
            return UtilityResponse::invalidFieldsInRequest($invalid_fields);
        }

        return null;
    }

    /**
     * Check the request to see if there are any fields in the request, if not
     * we simply throw an error
     *
     * @return JsonResponse|null
     */
    public static function checkForEmptyPatch(): ?JsonResponse
    {
        if (count(request()->all()) === 0) {
            return UtilityResponse::nothingToPatch();;
        }

        return null;
    }

    /**
     * Return the errors from the validator
     *
     * @param $validator
     * @param array $allowed_values
     *
     * @return JsonResponse|null
     */
    public static function validateAndReturnErrors(
        Validator $validator,
        array $allowed_values = []
    ): ?JsonResponse
    {
        if ($validator->fails() === true) {
            $validation_errors = [];

            foreach ($validator->errors()->toArray() as $field => $errors) {
                foreach ($errors as $error) {
                    $validation_errors[$field]['errors'][] = $error;
                }
            }

            if (count($allowed_values) > 0) {
                $validation_errors = array_merge_recursive($validation_errors, $allowed_values);
            }

            return UtilityResponse::validationErrors($validation_errors);
        }

        return null;
    }
}
