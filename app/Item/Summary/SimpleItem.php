<?php
declare(strict_types=1);

namespace App\Item\Summary;

use App\Models\Summary\ItemType\SimpleItem as ItemModel;
use Illuminate\Database\Eloquent\Model;

class SimpleItem extends AbstractItem
{

    /**
     * Return the parameters config string specific to the item type
     *
     * @return string
     */
    public function collectionParametersConfig(): string
    {
        return 'api.item-type-simple-item.summary-parameters.collection';
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
        return 'api.item-type-simple-item.summary-searchable';
    }
}
