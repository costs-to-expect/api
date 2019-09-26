<?php
declare(strict_types=1);

namespace App\Item;

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
     * Return the relevant item instance based on the provided
     * resource type id
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
        
    }
}
