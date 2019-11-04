<?php
declare(strict_types=1);

namespace App\Item\Summary;

use App\Models\Transformers\Transformer;
use Illuminate\Database\Eloquent\Model;

class AllocatedExpense extends AbstractItem
{

    /**
     * Return the parameters config string specific to the item type
     *
     * @return string
     */
    public function collectionParametersConfig(): string
    {
        return 'api.item-type-allocated-expense.summary-parameters.collection';
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
        // TODO: Implement conditionalParameterMinYear() method.
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
        // TODO: Implement conditionalParameterMaxYear() method.
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
     * Return the search parameters config string specific to the item type
     *
     * @return string
     */
    public function searchParametersConfig(): string
    {
        return 'api.item-type-allocated-expense.summary-searchable';
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
        // TODO: Implement transformer() method.
    }

    /**
     * Return the item type identifier
     *
     * @return string
     */
    public function type(): string
    {
        // TODO: Implement type() method.
    }
}
