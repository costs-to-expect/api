<?php
declare(strict_types=1);

namespace App\Item;

use App\Models\ItemTypeAllocatedExpense;
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
     * Return the parameters config string specific to the item type
     *
     * @return string
     */
    public function collectionParametersConfig(): string
    {
        return 'api.item-type-allocated-expense.parameters.collection';
    }

    /**
     * Return the model instance for the item type
     *
     * @return Model
     */
    public function model(): Model
    {
        return new ItemTypeAllocatedExpense();
    }

    /**
     * Return the patch fields config string specific to the item type
     *
     * @return string
     */
    public function patchFieldsConfig(): string
    {
        return 'api.item-type-allocated-expense.fields';
    }

    /**
     * Return the post fields config string specific to the item type
     *
     * @return string
     */
    public function postFieldsConfig(): string
    {
        return 'api.item-type-allocated-expense.fields';
    }

    /**
     * Return the search parameters config string specific to the item type
     *
     * @return string
     */
    public function searchParametersConfig(): string
    {
        return 'api.item-type-allocated-expense.searchable';
    }

    /**
     * Return the show parameters config string specific to the item type
     *
     * @return string
     */
    public function showParametersConfig(): string
    {
        return 'api.item-type-allocated-expense.parameters.item';
    }

    /**
     * Return the sort parameters config string specific to the item type
     *
     * @return string
     */
    public function sortParametersConfig(): string
    {
        return 'api.item-type-allocated-expense.sortable';
    }

    /**
     * Return the validation patch field messages config for the specific item
     * type
     *
     * @return string
     */
    public function validationPatchFieldMessagesConfig(): string
    {
        // TODO: Implement validationPatchFieldMessagesConfig() method.
    }

    /**
     * Return the validation patch fields config for the specific item type
     *
     * @return string
     */
    public function validationPatchFieldsConfig(): string
    {
        // TODO: Implement validationPatchFieldsConfig() method.
    }

    /**
     * Return the validation post field messages config for the specific item
     * type
     *
     * @return string
     */
    public function validationPostFieldMessagesConfig(): string
    {
        // TODO: Implement validationPostFieldMessagesConfig() method.
    }

    /**
     * Return the validation post fields config for the specific item type
     *
     * @return string
     */
    public function validationPostFieldsConfig(): string
    {
        // TODO: Implement validationPostFieldsConfig() method.
    }
}
