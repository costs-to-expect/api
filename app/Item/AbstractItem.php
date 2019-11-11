<?php
declare(strict_types=1);

namespace App\Item;

use App\Models\Transformers\Transformer;
use App\Validators\Request\Fields\Validator;
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
     * Create an save the item type data
     *
     * @param integer $id
     *
     * @return Model
     */
    abstract public function create($id): Model;

    /**
     * Fetch an instance of the item type model
     *
     * @param integer $id
     *
     * @return Model
     */
    abstract public function instance(int $id): Model;

    /**
     * Return the model instance for the item type
     *
     * @return Model
     */
    abstract public function model(): Model;

    /**
     * Return an array of the fields that can be PATCHed.
     *
     * @return array
     */
    abstract public function patchableFields(): array;

    /**
     * Return the patch fields config string specific to the item type
     *
     * @return string
     */
    abstract public function patchFieldsConfig(): string;

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

    /**
     * Update the item type data
     *
     * @param array $request
     * @param Model $instance
     *
     * @return bool
     */
    abstract public function update(array $request, Model $instance): bool;

    /**
     * Return the validator to use for the validation checks
     *
     * @return Validator
     */
    abstract public function validator(): Validator;
}
