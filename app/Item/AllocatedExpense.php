<?php
declare(strict_types=1);

namespace App\Item;

use Illuminate\Database\Eloquent\Model;

/**
 * The Interface for dealing with allocated expenses, everything should be
 * funneled through an instance of this class
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class AllocatedExpense extends AbstractItem
{
    /**
     * Return the collection parameters specific to the item type, these will
     * be merged with the default collection parameters
     *
     * @return array
     */
    public function collectionParameters(): array
    {
        // TODO: Implement collectionParameters() method.
    }

    /**
     * Return the model instance for the item type
     *
     * @return Model
     */
    public function model(): Model
    {
        // TODO: Implement model() method.
    }

    /**
     * Return the patch fields specific to the item type, these will be merged
     * with any default patch fields
     *
     * @return array
     */
    public function patchFields(): array
    {
        // TODO: Implement patchFields() method.
    }

    /**
     * Return the post fields specific to the item type, these will be merged
     * with any default post fields
     *
     * @return array
     */
    public function postFields(): array
    {
        // TODO: Implement postFields() method.
    }

    /**
     * Return the search parameters specific to the item type, these will be
     * merged with any default search parameters
     *
     * @return array
     */
    public function searchParameters(): array
    {
        // TODO: Implement searchParameters() method.
    }

    /**
     * Return the sort parameters specific to the item type, these will be
     * merged with any default sort parameters
     *
     * @return array
     */
    public function sortParameters(): array
    {
        // TODO: Implement sortParameters() method.
    }
}
