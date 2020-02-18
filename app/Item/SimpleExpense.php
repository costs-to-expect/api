<?php
declare(strict_types=1);

namespace App\Item;

use App\Models\ItemType\SimpleExpense as ItemModel;
use App\Models\Transformers\Transformer;
use App\Validators\Request\Fields\ItemType\SimpleExpense as ItemTypeSimpleExpenseValidator;
use App\Validators\Request\Fields\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

/**
 * The Interface for dealing with simple expenses, everything should be
 * funneled through an instance of this class
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
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
     * Return the minimum year for the conditional year filter, reviews the
     * item type data and returns the min value, if no data exists, defaults to
     * the current year
     *
     * @param integer $resource_id
     *
     * @return integer
     */
    public function conditionalParameterMinYear(int $resource_id): int
    {
        return (new ItemModel())->minimumEffectiveDateYear($resource_id);
    }

    /**
     * Return the minimum year for the conditional year filter, reviews the
     * item type data and returns the min value, if no data exists, defaults to
     * the current year
     *
     * @param integer $resource_id
     *
     * @return integer
     */
    public function conditionalParameterMaxYear(int $resource_id): int
    {
        return (new ItemModel())->maximumEffectiveDateYear($resource_id);
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
        $item_type = new ItemModel([
            'item_id' => $id,
            'name' => request()->input('name'),
            'description' => request()->input('description', null),
            'effective_date' => request()->input('effective_date'),
            'total' => request()->input('total'),
        ]);

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
        return (new ItemModel())->instance($id);
    }

    /**
     * Return the model instance for the item type
     *
     * @return Model
     */
    public function model(): Model
    {
        return new ItemModel();
    }

    /**
     * Return an array of the fields that can be PATCHed.
     *
     * @return array
     */
    public function patchableFields(): array
    {
        return array_keys(
            Config::get('api.item-type-simple-expense.validation.PATCH.fields'),
        );
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
     * Return the transformer for the specific item type
     *
     * @param array $data_to_transform
     *
     * @return Transformer
     */
    public function transformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\ItemType\SimpleExpense($data_to_transform);
    }

    /**
     * Return the item type identifier
     *
     * @return string
     */
    public function type(): string
    {
        return 'simple-expense';
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
        foreach ($request as $key => $value) {
            $instance->$key = $value;
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
        return new ItemTypeSimpleExpenseValidator();
    }
}
