<?php

declare(strict_types=1);

namespace App\HttpRequest\Validate;

/**
 * Small non-negative integer validation utility
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2025
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Integer
{
    /**
     * Check the provided value is a valid non-negative integer, i.e. it
     * only contains digits. A loose (int) cast would also accept values
     * such as "5abc" (5) or "" (0), so we can't rely on that alone
     *
     * @param mixed $value Value to check
     *
     * @return bool
     */
    public static function isValid($value): bool
    {
        return (is_string($value) || is_int($value)) && ctype_digit((string) $value);
    }
}
