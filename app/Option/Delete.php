<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

/**
 * Helper class to generate the data required to build the OPTIONS required for
 * a single HTTP Verb, in this case DELETE
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Delete
{
    /**
     * @var Delete
     */
    static private $instance;

    /**
     * @var boolean
     */
    static private $authentication;

    /**
     * @var string
     */
    static private $description;

    static private function reset()
    {
        self::$authentication = false;
        self::$description = null;
    }

    static public function init(): Delete
    {
        if (self::$instance === null) {
            self::$instance = new Delete();
            self::$instance->reset();
        }

        return self::$instance;
    }

    static public function setAuthenticationRequired(
        bool $status = false
    ): Delete
    {
        self::$authentication = $status;

        return self::$instance;
    }

    static public function setDescription(
        string $localisation_path
    ): Delete
    {
        self::$description = trans($localisation_path);

        return self::$instance;
    }

    static public function option(): array
    {
        return [
            'DELETE' => [
                'description' => self::$description,
                'authentication_required' => self::$authentication
            ]
        ];
    }
}
