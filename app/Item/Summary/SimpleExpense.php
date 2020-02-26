<?php
declare(strict_types=1);

namespace App\Item\Summary;

use App\Models\ItemType\Summary\SimpleExpense as ItemModel;
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
        return 'api.item-type-simple-expense.summary-parameters.collection';
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
        return 'api.item-type-simple-expense.summary-searchable';
    }
}
