<?php
declare(strict_types=1);

namespace App\Request\Validate;

/**
 * Small boolean validation utility
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Boolean
{
    /**
     * Converts the provided value to a boolean, valid options are 1, true,
     * "true" and "off". We look for the positive values and return anything
     * else as FALSE
     *
     * @param $value
     *
     * @return bool
     */
    public static function convertedValue($value): bool
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
    public static function isConvertible($value): bool
    {
        $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);

        return $filtered === true || $filtered === false;
    }
}
