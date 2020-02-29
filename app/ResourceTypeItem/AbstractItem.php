<?php
declare(strict_types=1);

namespace App\ResourceTypeItem;

use App\Interfaces\ResourceTypeItemModel;
use App\Models\Transformers\Transformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

/**
 * Base class for each item type, contains the required methods
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
abstract class AbstractItem
{
    /**
     * Return the collection parameters specific to the item type, these will
     * be merged with the default collection parameters
     *
     * @return array
     */
    public function collectionParameters(): array
    {
         return Config::get($this->collectionParametersConfig());
    }

    /**
     * Return the parameters config string specific to the item type
     *
     * @return string
     */
    abstract public function collectionParametersConfig(): string;

    /**
     * Return the collection parameters specific to the item type, these will
     * be merged with the default collection parameters
     *
     * @return array
     */
    public function collectionParametersKeys(): array
    {
        $params = [];
        foreach (Config::get($this->collectionParametersConfig()) as $key => $param) {
            $params[$param['parameter']] = null;
        }

        return $params;
    }

    /**
     * Return the filter parameters specific to the item type
     *
     * @return array
     */
    public function filterParameters(): array
    {
        return Config::get($this->filterParametersConfig());
    }

    /**
     * Return the filter parameters config string specific to the item type
     *
     * @return string
     */
    abstract public function filterParametersConfig(): string;

    /**
     * Return the model instance for resource type item type
     *
     * @return ResourceTypeItemModel
     */
    abstract public function model(): ResourceTypeItemModel;

    /**
     * Return the search parameters specific to the item type, these will be
     * merged with any default search parameters
     *
     * @return array
     */
    public function searchParameters(): array
    {
        return Config::get($this->searchParametersConfig());
    }

    /**
     * Return the search parameters config string specific to the item type
     *
     * @return string
     */
    abstract public function searchParametersConfig(): string;

    /**
     * Return the sort parameters specific to the item type, these will be
     * merged with any default sort parameters
     *
     * @return array
     */
    public function sortParameters(): array
    {
        return Config::get($this->sortParametersConfig());
    }

    /**
     * Return the sort parameters config string specific to the item type
     *
     * @return string
     */
    abstract public function sortParametersConfig(): string;

    /**
     * Return the transformer for the specific item type
     *
     * @param array $data_to_transform
     *
     * @return Transformer
     */
    abstract public function transformer(array $data_to_transform): Transformer;

    /**
     * Return the item type identifier
     *
     * @return string
     */
    abstract public function type(): string;
}
