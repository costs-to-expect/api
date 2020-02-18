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
     * Checks a value and see if a boolean is returned after a call to
     * filter_var($value, FILTER_VALIDATE_BOOLEAN)
     *
     * @param $value
     *
     * @return bool
     */
    static public function booleanValue($value): bool
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN) === true) {
            return true;
        }

        return false;
    }

    /**
     * Check to see if a value is a valid boolean, uses filter_var so true, 1,
     * "true" and "on" are all valid positive boolean values, off values
     * are false, 0, "false" and "off"
     *
     * @param mixed $value Value to check to see if it is a possible boolean
     *
     * @return bool
     */
    static public function isBooleanValue($value): bool
    {
        $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);

        if ($filtered === true || $filtered === false) {
            return true;
        }

        return false;
    }
}
