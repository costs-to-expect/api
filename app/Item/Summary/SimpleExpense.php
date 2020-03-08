<?php
declare(strict_types=1);

namespace App\Item\Summary;

use App\Interfaces\Item\ISummaryModel\ISummaryModel;
use App\Models\ItemType\Summary\SimpleExpense as ItemModel;

class SimpleExpense extends AbstractItem
{

    /**
     * Return the parameters config string specific to the item type
     *
     * @return string
     */
    public function collectionParametersConfig(): string
    {
        return 'api.item-type-simple-expense.summary-parameters.collection';
    }

    /**
     * Return the filter parameters config string specific to the item type
     *
     * @return string
     */
    public function filterParametersConfig(): string
    {
        return 'api.item-type-simple-expense.summary-filterable';
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
        return 'api.item-type-simple-expense.summary-searchable';
    }
}
