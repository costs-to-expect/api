<?php
declare(strict_types=1);

namespace App\Utilities;

/**
 * General utility class, holder of all the methods that do not seem to fit
 * anywhere else, typically, new classes will spawn from methods within this
 * class
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class General
{
    /**
     * Converts the provided value to a boolean, valid options are 1, true,
     * "true" and "off". We look for the positive values are return anything
     * else as FALSE
     *
     * @param $value
     *
     * @return bool
     */
    public static function convertedBooleanValue($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) === true;
    }

    /**
     * Check to see if the provided value can be turned into a boolean,
     * we use filter_var s0 true, 1, "true" and "on" are all valid positive
     * boolean values, valid false values are false, 0, "false" and "off"
     *
     * @param mixed $value Value to check to see if it is a possible boolean
     *
     * @return bool
     */
    public static function isConvertibleToBoolean($value): bool
    {
        $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);

        return $filtered === true || $filtered === false;
    }
}
