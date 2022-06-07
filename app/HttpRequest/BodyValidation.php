<?php
declare(strict_types=1);

namespace App\HttpRequest;

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
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class BodyValidation
{
    /**
     * Check to see if any of the provided fields are invalid, we throw an error
     * if there are any invalid fields in the request, simpler to fail hard than
     * simply ignore invalid values
     *
     * @param  array  $patchable_fields
     *
     * @return JsonResponse|null
     */
    public static function checkForInvalidFields(array $patchable_fields): array
    {
        $invalid_fields = [];
        foreach (request()->all() as $key => $value) {
            if (in_array($key, $patchable_fields, true) === false) {
                $invalid_fields[] = $key;
            }
        }

        return $invalid_fields;
    }

    public static function returnValidationErrors( // Rename this
        Validator $validator,
        array $allowed_values = []
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

        return \App\HttpResponse\Responses::validationErrors($validation_errors);
    }
}
