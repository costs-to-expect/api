<?php
declare(strict_types=1);

namespace App\ResourceTypeItem\Summary;

use App\Interfaces\ResourceTypeItem\ISummaryModel;
use App\Models\ResourceTypeItem\Summary\SimpleItem as ItemModel;

class SimpleItem extends AbstractItem
{

    /**
     * Return the parameters config string specific to the item type
     *
     * @return string
     */
    public function collectionParametersConfig(): string
    {
        return 'api.resource-type-item-type-simple-item.summary-parameters';
    }

    /**
     * Return the filter parameters config string
     *
     * @return string
     */
    public function filterParametersConfig(): string
    {
        return 'api.resource-type-item-type-simple-item.summary-filterable';
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
        return 'api.resource-type-item-type-simple-item.summary-searchable';
    }
}
