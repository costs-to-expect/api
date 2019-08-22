<?php
declare(strict_types=1);

namespace App\Utilities;

use App\Utilities\Response as UtilityResponse;
use Illuminate\Http\JsonResponse;

/**
 * PATCH utility class, helper methods to reduce duplication
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Patch
{
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
        } else {
            return null;
        }
    }
}
