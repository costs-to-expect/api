<?php
declare(strict_types=1);

namespace App\Item\ResourceTypeItem;

use App\Models\ResourceTypeItemType\SimpleItem as ItemModel;
use App\Models\Transformers\Transformer;
use Illuminate\Database\Eloquent\Model;

/**
 * The Interface for dealing with simple item, everything should be
 * funneled through an instance of this class
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SimpleItem extends AbstractItem
{
    /**
     * Return the parameters config string specific to the item type
     *
     * @return string
     */
    public function collectionParametersConfig(): string
    {
        return 'api.resource-type-item-type-simple-item.parameters.collection';
    }

    /**
     * Return the model instance for resource type item type
     *
     * @return Model
     */
    public function model(): Model
    {
        return new ItemModel();
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
        return new \App\Models\Transformers\ResourceTypeItemType\SimpleItem($data_to_transform);
    }

    /**
     * Return the item type identifier
     *
     * @return string
     */
    public function type(): string
    {
        return 'simple-item';
    }

    /**
     * Return the search parameters config string specific to the item type
     *
     * @return string
     */
    public function searchParametersConfig(): string
    {
        return 'api.resource-type-item-type-simple-item.searchable';
    }

    /**
     * Return the sort parameters config string specific to the item type
     *
     * @return string
     */
    public function sortParametersConfig(): string
    {
        return 'api.resource-type-item-type-simple-item.sortable';
    }
}
