<?php
declare(strict_types=1);

namespace App\Item\Summary;

use App\Models\Transformers\Transformer;
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
     * Return the minimum year for the conditional year filter, reviews the
     * item type data and returns the min value, if no data exists, defaults to
     * the current year
     *
     * @param integer $resource_id
     *
     * @return integer
     */
    abstract public function conditionalParameterMinYear(int $resource_id): int;

    /**
     * Return the minimum year for the conditional year filter, reviews the
     * item type data and returns the min value, if no data exists, defaults to
     * the current year
     *
     * @param integer $resource_id
     *
     * @return integer
     */
    abstract public function conditionalParameterMaxYear(int $resource_id): int;

    /**
     * Return the model instance for the item type
     *
     * @return Model
     */
    abstract public function model(): Model;

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
