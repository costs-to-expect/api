<?php
declare(strict_types=1);

namespace App\Validators\Request\Routes;

use App\Models\ResourceTypeAccess;

/**
 * Validate the route params to an item type
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemType
{
    /**
     * Validate that the user is able to view the requested item type
     *
     * @param string|int $item_type_id
     *
     * @return boolean
     */
    static public function existsToUserForViewing($item_type_id): bool
    {
        if (
            $item_type_id === 'nill' ||
            (new ResourceTypeAccess())->itemTypeExistsToUser($item_type_id) === false
        ) {
            return false;
        }

        return true;
    }
}
