<?php
declare(strict_types=1);

namespace App\Option;

/**
 * Base class for the Option classes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
abstract class Option
{
    static protected $instance;

    /**
     * @var boolean
     */
    static protected $authentication;

    /**
     * @var string
     */
    static protected $description;

    static protected function resetBase()
    {
        self::$authentication = false;
        self::$description = null;
    }

    static public function setAuthenticationRequired(
        bool $status = false
    ): Option
    {
        self::$authentication = $status;

        return self::$instance;
    }

    static public function setDescription(
        string $localisation_path
    ): Option
    {
        self::$description = trans($localisation_path);

        return self::$instance;
    }

    static abstract protected function build();

    static abstract public function option(): array;
}
