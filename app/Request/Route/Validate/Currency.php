<?php
declare(strict_types=1);

namespace App\Request\Route\Validate;

use App\Models\ResourceAccess;

/**
 * Validate the route params to a currency
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Currency
{
    /**
     * Validate that the user is able to view the requested currency
     *
     * @param string|int $currency_id
     *
     * @return boolean
     */
    public static function existsToUserForViewing($currency_id): bool
    {
        return !(
            (new ResourceAccess())->currencyExistsToUser((int) $currency_id) === false
        );
    }
}
