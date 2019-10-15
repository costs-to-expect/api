<?php
declare(strict_types=1);

namespace App\Validators\Request\Routes;

use App\Models\ItemType as ItemTypeModel;

/**
 * Validate the route params to an item type
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
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
            (new ItemTypeModel())->existsToUser($item_type_id) === false
        ) {
            return false;
        }

        return true;
    }
}
