<?php
declare(strict_types=1);

namespace App\Item;

use App\Models\ItemTypeAllocatedExpense;
use Illuminate\Database\Eloquent\Model;

/**
 * The Interface for dealing with simple expenses, everything should be
 * funneled through an instance of this class
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SimpleExpense extends AbstractItem
{
    /**
     * Return the parameters config string specific to the item type
     *
     * @return string
     */
    public function collectionParametersConfig(): string
    {
        return 'api.item-type-simple-expense.parameters.collection';
    }

    /**
     * Create an save the item type data
     *
     * @param integer $id
     *
     * @return Model
     */
    public function create($id): Model
    {
        $item_type = new ItemTypeAllocatedExpense([
            'item_id' => $id,
            'name' => request()->input('name'),
            'description' => request()->input('description', null),
            'effective_date' => request()->input('effective_date'),
            'publish_after' => request()->input('publish_after', null),
            'total' => request()->input('total'),
            'percentage' => request()->input('percentage', 100),
        ]);

        $item_type->setActualisedTotal(
            request()->input('total'),
            request()->input('percentage', 100)
        );

        $item_type->save();

        return $item_type;
    }

    /**
     * Fetch an instance of the item type model
     *
     * @param integer $id
     *
     * @return Model
     */
    public function instance(int $id): Model
    {
        return (new ItemTypeAllocatedExpense())->instance($id);
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
        return 'api.item-type-simple-expense.fields';
    }

    /**
     * Return the post fields config string specific to the item type
     *
     * @return string
     */
    public function postFieldsConfig(): string
    {
        return 'api.item-type-simple-expense.fields';
    }

    /**
     * Return the search parameters config string specific to the item type
     *
     * @return string
     */
    public function searchParametersConfig(): string
    {
        return 'api.item-type-simple-expense.searchable';
    }

    /**
     * Return the show parameters config string specific to the item type
     *
     * @return string
     */
    public function showParametersConfig(): string
    {
        return 'api.item-type-simple-expense.parameters.item';
    }

    /**
     * Return the sort parameters config string specific to the item type
     *
     * @return string
     */
    public function sortParametersConfig(): string
    {
        return 'api.item-type-simple-expense.sortable';
    }

    /**
     * Update the item type data
     *
     * @param array $request
     * @param Model $instance
     *
     * @return bool
     */
    public function update(array $request, Model $instance): bool
    {
        $update_actualised = false;
        foreach ($request as $key => $value) {
            $instance->$key = $value;

            if (in_array($key, ['total', 'percentage']) === true) {
                $update_actualised = true;
            }
        }

        if ($update_actualised === true) {
            $instance->setActualisedTotal($instance->total, $instance->percentage);
        }

        return $instance->save();
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
