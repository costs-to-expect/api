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
class ItemInterfaceFactory
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
    static protected function itemInterface(
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

    /**
     * Return the relevant item instance based on the provided resource type id,
     * throws an exception if the item type interface cannot be returned
     *
     * @param integer $resource_type_id
     *
     * @return ResourceTypeItem\AbstractItem
     * @throws Exception
     */
    static protected function resourceTypeItemInterface(
        int $resource_type_id
    ): ResourceTypeItem\AbstractItem
    {
        $item_type = (new ResourceTypeItemType())->itemType($resource_type_id);

        if ($item_type !== null) {
            switch ($item_type) {
                case 'allocated-expense':
                    return new ResourceTypeItem\AllocatedExpense();
                    break;

                case 'simple-expense':
                    return new ResourceTypeItem\SimpleExpense();
                    break;

                default:
                    throw new Exception('Unable to load the relevant resource type item type', 500);
                    break;
            }
        } else {
            throw New Exception('No relevant resource type item type defined for the resource type', 500);
        }
    }

    /**
     * Return the relevant item interface
     *
     * @param integer $resource_type_id
     *
     * @return AbstractItem
     */
    static public function item(int $resource_type_id): AbstractItem
    {
        try {
            return self::itemInterface($resource_type_id);
        } catch (Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    /**
     * Return the relevant resource type item interface
     *
     * @param integer $resource_type_id
     *
     * @return \App\Item\ResourceTypeItem\AbstractItem
     */
    static public function resourceTypeItem(int $resource_type_id): \App\Item\ResourceTypeItem\AbstractItem
    {
        try {
            return self::resourceTypeItemInterface($resource_type_id);
        } catch (Exception $e) {
            abort(500, $e->getMessage());
        }
    }
}
