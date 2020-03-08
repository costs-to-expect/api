<?php
declare(strict_types=1);

namespace App\ResourceTypeItem\Summary;

use App\Models\ResourceTypeItem\Summary\SimpleExpense as ItemModel;
use Illuminate\Database\Eloquent\Model;

class SimpleExpense extends AbstractItem
{

    /**
     * Return the parameters config string specific to the item type
     *
     * @return string
     */
    public function collectionParametersConfig(): string
    {
        return 'api.resource-type-item-type-simple-expense.summary-parameters.collection';
    }

    /**
     * Return the filter parameters config string
     *
     * @return string
     */
    public function filterParametersConfig(): string
    {
        return 'api.resource-type-item-type-simple-expense.summary-filterable';
    }

    /**
     * Return the model instance for the item type
     *
     * @return Model
     */
    public function model(): Model
    {
        return new ItemModel;
    }

    /**
     * Return the search parameters config string specific to the item type
     *
     * @return string
     */
    public function searchParametersConfig(): string
    {
        return 'api.resource-type-item-type-simple-expense.summary-searchable';
    }
}
