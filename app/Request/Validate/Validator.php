<?php
declare(strict_types=1);

namespace App\Request\Validate;

use App\Item\AbstractItem;
use App\Request\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Base validator class, sets up the interface and includes helper methods
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
abstract class Validator
{
    protected Hash $hash;

    protected AbstractItem $item;

    public function __construct()
    {
        $this->hash = new Hash();
    }

    /**
     * Fetch the validation error messages from the requested configuration
     * file and translate the message strings
     *
     * @param string $config_key
     *
     * @return array
     */
    protected function translateMessages(string $config_key): array
    {
        $messages = [];

        foreach (Config::get($config_key) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        return $messages;
    }

    /**
     * Check to ensure we have all the required indexes, check the required
     * keys against the provided keys
     *
     * @param array $required
     * @param array $provided
     */
    protected function requiredIndexes(
        array $required = [],
        array $provided = []
    ): void
    {
        foreach ($provided as $key => $value) {
            if (in_array($key, $required, true) === false) {
                abort(500, 'Indexes missing in options array for validator');
            }
        }
    }

    /**
     * Return a valid validator object for a create (POST) request
     *
     * @param array $options
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    abstract public function create(array $options = []): \Illuminate\Contracts\Validation\Validator;

    /**
     * Return a valid validator object for a update (PATCH) request
     *
     * @param array $options
     *
     * @return \Illuminate\Contracts\Validation\Validator|null
     */
    abstract public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator;

    /**
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function createItemValidator(): \Illuminate\Contracts\Validation\Validator
    {
        $messages = [];
        foreach ($this->item->validationPostableFieldMessages() as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        return ValidatorFacade::make(
            request()->all(),
            $this->item->validationPostableFields(),
            $messages
        );
    }

    /**
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function updateItemValidator(): ?\Illuminate\Contracts\Validation\Validator
    {
        $messages = [];
        foreach ($this->item->validationPatchableFieldMessages() as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        return ValidatorFacade::make(
            request()->all(),
            $this->item->validationPatchableFields(),
            $messages
        );
    }
}