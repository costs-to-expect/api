<?php
declare(strict_types=1);

namespace App\Item\Summary;

use App\Interfaces\Item\ISummaryModel\ISummaryModel;
use App\Models\Item\Summary\AllocatedExpense as ItemModel;

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
     * Return the filter parameters config string specific to the item type
     *
     * @return string
     */
    public function filterParametersConfig(): string
    {
        return 'api.item-type-allocated-expense.summary-filterable';
    }

    /**
     * Return the model instance for the item type
     *
     * @return ISummaryModel
     */
    public function model(): ISummaryModel
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
        return 'api.item-type-allocated-expense.summary-searchable';
    }
}
