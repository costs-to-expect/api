<?php
declare(strict_types=1);

namespace App\Item;

use App\Models\ItemTypeAllocatedExpense;
use App\Models\ResourceTypeItemTypeAllocatedExpense;
use App\Models\Transformers\Transformer;
use App\Validators\Request\Fields\ItemTypeAllocatedExpense as ItemTypeAllocatedExpenseValidator;
use App\Validators\Request\Fields\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

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
     * Return an array of the fields that can be PATCHed.
     *
     * @return array
     */
    public function patchableFields(): array
    {
        return array_keys(
            Config::get('api.item-type-allocated-expense.validation.PATCH.fields'),
        );
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
     * Return the model instance for resource type item type
     *
     * @return Model
     */
    public function resourceTypeItemModel(): Model
    {
        return new ResourceTypeItemTypeAllocatedExpense();
    }

    /**
     * Return the transformer for the specific item type
     *
     * @param array $data_to_transform
     *
     * @return Transformer
     */
    public function resourceTypeItemTransformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\ResourceTypeItemTypeAllocatedExpense($data_to_transform);
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
     * Return the transformer for the specific item type
     *
     * @param array $data_to_transform
     *
     * @return Transformer
     */
    public function transformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\ItemTypeAllocatedExpense($data_to_transform);
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
     * Return the validator to use for the validation checks
     *
     * @return Validator
     */
    public function validator(): Validator
    {
        return new ItemTypeAllocatedExpenseValidator();
    }
}
