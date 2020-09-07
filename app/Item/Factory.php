<?php
declare(strict_types=1);

namespace App\Item;

use App\Models\ResourceTypeItemType;
use Exception;

/**
 * Factory to help instantiating the relevant `item` class
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Factory
{
    /**
     * @param int $resource_type_id
     *
     * @return string
     */
    static protected function itemType(int $resource_type_id)
    {
        return (new ResourceTypeItemType())->itemType($resource_type_id);
    }

    /**
     * Return the relevant item summary instance based on the provided resource
     * type id, throws an exception if the item type interface cannot be returned
     *
     * @param integer $resource_type_id
     *
     * @return Summary\AbstractItem
     * @throws Exception
     */
    static protected function itemSummaryInterface(
        int $resource_type_id
    ): Summary\AbstractItem
    {
        $item_type = self::itemType($resource_type_id);

        if ($item_type !== null) {
            switch ($item_type) {
                case 'allocated-expense':
                    return new Summary\AllocatedExpense();
                    break;

                case 'simple-expense':
                    return new Summary\SimpleExpense();
                    break;

                case 'simple-item':
                    return new Summary\SimpleItem();
                    break;

                default:
                    throw new Exception('Unable to load the relevant summary item type', 500);
                    break;
            }
        } else {
            throw New Exception('No relevant summary item type defined for the resource type', 500);
        }
    }

    /**
     * Return the relevant item instance based on the provided resource type id,
     * throws an exception if the item type interface cannot be returned
     *
     * @param integer $resource_type_id
     *
     * @return \App\ResourceTypeItem\AbstractItem
     * @throws Exception
     */
    static protected function resourceTypeItemInterface(
        int $resource_type_id
    ): \App\ResourceTypeItem\AbstractItem
    {
        $item_type = self::itemType($resource_type_id);

        if ($item_type !== null) {
            switch ($item_type) {
                case 'allocated-expense':
                    return new \App\ResourceTypeItem\AllocatedExpense();
                    break;

                case 'simple-expense':
                    return new \App\ResourceTypeItem\SimpleExpense();
                    break;

                case 'simple-item':
                    return new \App\ResourceTypeItem\SimpleItem();
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
     * @return Summary\AbstractItem
     */
    static public function summaryItem(int $resource_type_id): Summary\AbstractItem
    {
        try {
            return self::itemSummaryInterface($resource_type_id);
        } catch (Exception $e) {
            abort(500, $e->getMessage());
        }
    }
}
