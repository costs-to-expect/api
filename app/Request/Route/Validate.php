<?php
declare(strict_types=1);

namespace App\Request\Route;

/**
 * Validate the set route parameters, redirect to 404 if invalid
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Validate
{

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * (In this case that doesn't happen as anyone can see the item type)
     *
     * @param $item_type_id
     */
    public static function itemType($item_type_id)
    {
        if (Validator\ItemType::existsToUserForViewing($item_type_id) === false) {
            \App\Response\Responses::notFound(trans('entities.item-type'));
        }
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * (In this case that doesn't happen as anyone can see the item type)
     *
     * @param $item_type_id
     * @param $item_subtype_id
     */
    public static function itemSubType($item_type_id, $item_subtype_id)
    {
        if (Validator\ItemSubtype::existsToUserForViewing($item_type_id, $item_subtype_id) === false) {
            \App\Response\Responses::notFound(trans('entities.item-type'));
        }
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * (In this case that doesn't happen as anyone can see the currency)
     *
     * @param $currency_id
     */
    public static function currency($currency_id)
    {
        if (Validator\Currency::existsToUserForViewing($currency_id) === false) {
            \App\Response\Responses::notFound(trans('entities.currency'));
        }
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * (In this case that doesn't happen as anyone can see the queue)
     *
     * @param $queue_id
     */
    public static function queue($queue_id)
    {
        if (Validator\Queue::existsToUserForViewing($queue_id) === false) {
            \App\Response\Responses::notFound(trans('entities.queue'));
        }
    }
}
