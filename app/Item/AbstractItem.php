<?php
declare(strict_types=1);

namespace App\Item;

use Illuminate\Database\Eloquent\Model;

/**
 * Base class for each item type, contains the required methods
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
abstract class AbstractItem
{
    /**
     * Return the collection parameters specific to the item type, these will
     * be merged with the default collection parameters
     *
     * @return array
     */
    abstract public function collectionParameters(): array;

    /**
     * Return the model instance for the item type
     *
     * @return Model
     */
    abstract public function model(): Model;

    /**
     * Return the patch fields specific to the item type, these will be merged
     * with any default patch fields
     *
     * @return array
     */
    abstract public function patchFields(): array;

    /**
     * Return the post fields specific to the item type, these will be merged
     * with any default post fields
     *
     * @return array
     */
    abstract public function postFields(): array;

    /**
     * Return the search parameters specific to the item type, these will be
     * merged with any default search parameters
     *
     * @return array
     */
    abstract public function searchParameters(): array;

    /**
     * Return the sort parameters specific to the item type, these will be
     * merged with any default sort parameters
     *
     * @return array
     */
    abstract public function sortParameters(): array;
}
