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
class ItemInterfaceFactory
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
        $item_type = self::itemType($resource_type_id);

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
     * @return ResourceTypeItem\AbstractItem
     * @throws Exception
     */
    static protected function resourceTypeItemInterface(
        int $resource_type_id
    ): ResourceTypeItem\AbstractItem
    {
        $item_type = self::itemType($resource_type_id);

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
     * Return the relevant item instance based on the provided resource type id,
     * throws an exception if the item type interface cannot be returned
     *
     * @param integer $resource_type_id
     *
     * @return ResourceTypeItem\Summary\AbstractItem
     * @throws Exception
     */
    static protected function resourceTypeItemSummaryInterface(
        int $resource_type_id
    ): ResourceTypeItem\Summary\AbstractItem
    {
        $item_type = self::itemType($resource_type_id);

        if ($item_type !== null) {
            switch ($item_type) {
                case 'allocated-expense':
                    return new ResourceTypeItem\Summary\AllocatedExpense();
                    break;

                case 'simple-expense':
                    return new ResourceTypeItem\Summary\SimpleExpense();
                    break;

                default:
                    throw new Exception('Unable to load the relevant resource type item summary type', 500);
                    break;
            }
        } else {
            throw New Exception('No relevant resource type item summary type defined for the resource type', 500);
        }
    }

    /**
     * Return the relevant item interface based on the item type asigned to the
     * given resource id
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

    /**
     * Return the relevant resource type item summary interface
     *
     * @param integer $resource_type_id
     *
     * @return \App\Item\ResourceTypeItem\Summary\AbstractItem
     */
    static public function summaryResourceTypeItem(int $resource_type_id): \App\Item\ResourceTypeItem\Summary\AbstractItem
    {
        try {
            return self::resourceTypeItemSummaryInterface($resource_type_id);
        } catch (Exception $e) {
            abort(500, $e->getMessage());
        }
    }
}
