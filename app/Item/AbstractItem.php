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
     * Return the patch fields specific to the item type, these will be merged
     * with any default patch fields
     *
     * @return array
     */
    public function patchFields(): array
    {
        return Config::get($this->patchFields());
    }

    /**
     * Return the patch fields config string specific to the item type
     *
     * @return string
     */
    abstract public function patchFieldsConfig(): string;

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
     * Return the validation patch field message for the specific item type
     *
     * @return array
     */
    public function validationPatchFieldMessages(): array
    {
        return Config::get($this->validationPatchFieldMessagesConfig());
    }

    /**
     * Return the validation patch field messages config for the specific item
     * type
     *
     * @return string
     */
    abstract public function validationPatchFieldMessagesConfig(): string;

    /**
     * Return the validation patch fields for the specific item type
     *
     * @return array
     */
    public function validationPatchFields(): array
    {
        return Config::get($this->validationPatchFieldsConfig());
    }

    /**
     * Return the validation patch fields config for the specific item type
     *
     * @return string
     */
    abstract public function validationPatchFieldsConfig(): string;

    /**
     * Return the validation post field message for the specific item type
     *
     * @return array
     */
    public function validationPostFieldMessages(): array
    {
        return Config::get($this->validationPatchFieldMessagesConfig());
    }

    /**
     * Return the validation post field messages config for the specific item
     * type
     *
     * @return string
     */
    abstract public function validationPostFieldMessagesConfig(): string;

    /**
     * Return the validation post fields for the specific item type
     *
     * @return array
     */
    public function validationPostFields(): array
    {
        return Config::get($this->validationPostFieldsConfig());
    }

    /**
     * Return the validation post fields config for the specific item type
     *
     * @return string
     */
    abstract public function validationPostFieldsConfig(): string;
}
