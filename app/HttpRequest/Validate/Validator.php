<?php

declare(strict_types=1);

namespace App\HttpRequest\Validate;

use App\HttpRequest\Hash;
use Illuminate\Support\Facades\Config;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
abstract class Validator
{
    protected Hash $hash;

    public function __construct()
    {
        $this->hash = new Hash();
    }

    protected function translateMessages(string $config_key): array
    {
        $messages = [];

        foreach (Config::get($config_key) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        return $messages;
    }

    protected function requiredIndexes(
        array $required = [],
        array $provided = []
    ): void {
        foreach ($provided as $key => $value) {
            if (in_array($key, $required, true) === false) {
                abort(500, 'Indexes missing in options array for validator');
            }
        }
    }

    abstract public function create(array $options = []): \Illuminate\Contracts\Validation\Validator;

    abstract public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator;
}
