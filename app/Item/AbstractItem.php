<?php
declare(strict_types=1);

namespace App\Item;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

/**
 * Base class for each item type, contains the required methods
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
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
     * Return the model instance for the item type
     *
     * @return Model
     */
    abstract public function model(): Model;

    /**
     * Return the patch fields specific to the item type, these will be merged
     * with any default patch fields
     *
     * @return array
     */
    abstract public function patchFields(): array;

    /**
     * Return the post fields specific to the item type, these will be merged
     * with any default post fields
     *
     * @return array
     */
    public function postFields(): array
    {
        return Config::get($this->postFieldsConfig());
    }

    /**
     * Return the post fields config string specific to the item type
     *
     * @return string
     */
    abstract public function postFieldsConfig(): string;

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
     * Return the show parameters specific to the item type, these will
     * be merged with the default show parameters
     *
     * @return array
     */
    public function showParameters(): array
    {
         return Config::get($this->showParametersConfig());
    }

    /**
     * Return the show parameters config string specific to the item type
     *
     * @return string
     */
    abstract public function showParametersConfig(): string;

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
}
