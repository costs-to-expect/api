<?php
declare(strict_types=1);

namespace App\Item;

use App\Models\ResourceTypeItemType;
use Exception;

/**
 * Factory to help instantiating the relevant `item` class
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemFactory
{
    /**
     * Return the relevant item instance based on the provided resource type id,
     * throws an exception if the item type interface cannot be returned
     *
     * @param integer $resource_type_id
     *
     * @return AbstractItem
     * @throws Exception
     */
    static public function getItemInterface(
        int $resource_type_id
    ): AbstractItem
    {
        $item_type = (new ResourceTypeItemType())->itemType($resource_type_id);

        if ($item_type !== null) {
            switch ($item_type) {
                case 'allocated-expense':
                    return new AllocatedExpense();
                    break;

                case 'simple-expense':
                    return new SimpleExpense();
                    break;

                default:
                    throw new Exception('Unable to load the relevant item type', 500);
                    break;
            }
        } else {
            throw New Exception('No relevant item type defined for the resource type', 500);
        }
    }
}
