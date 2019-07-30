<?php
declare(strict_types=1);

namespace App\Validators\Request\Fields;

use App\Utilities\Hash;
use Illuminate\Support\Facades\Config;

/**
 * Base validator class, sets up the interface and includes helper methods
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
abstract class Validator
{
    protected $hash;

    public function __construct()
    {
        $this->hash = new Hash();
    }

    /**
     * Fetch the messages from the requested configuration file and translate
     * the message strings
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
        };

        return $messages;
    }

    protected function requiredIndexes(array $required = [], array $provided = [])
    {
        foreach ($provided as $key => $value) {
            if (in_array($key, $required) === false) {
                abort(500, 'Indexes missing in options array for validator');
            }
        }
    }

    abstract public function create(array $options = []): \Illuminate\Contracts\Validation\Validator;

    abstract public function update(array $options = []): \Illuminate\Contracts\Validation\Validator;
}
