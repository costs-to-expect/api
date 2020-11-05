<?php
declare(strict_types=1);

namespace App\Request\Route\Validator;

use App\Models\ResourceTypeAccess;

/**
 * Validate the route params to a currency
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
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
            (new ResourceTypeAccess())->currencyExistsToUser((int) $currency_id) === false
        );
    }
}
