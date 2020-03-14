<?php
declare(strict_types=1);

namespace App\ResourceTypeItem\Summary;

use App\Interfaces\ResourceTypeItem\ISummaryModel;
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
     * Return the collection parameters specific to the item type
     *
     * @return array
     */
    public function collectionParametersKeys(): array
    {
        $params = [];
        foreach (Config::get($this->collectionParametersConfig()) as $key => $param) {
            $params[] = $param['parameter'];
        }

        return $params;
    }

    /**
     * Return the parameters config string specific to the item type
     *
     * @return string
     */
    abstract public function collectionParametersConfig(): string;

    /**
     * Return the filter parameters
     *
     * @return array
     */
    public function filterParameters(): array
    {
        return Config::get($this->filterParametersConfig());
    }

    /**
     * Return the filter parameters config string
     *
     * @return string
     */
    abstract public function filterParametersConfig(): string;

    /**
     * Return the model instance for the item type
     *
     * @return ISummaryModel
     */
    abstract public function model(): ISummaryModel;

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
}
