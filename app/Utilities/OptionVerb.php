<?php
declare(strict_types=1);

namespace App\Utilities;

/**
 * Helper class to generate the data required to build the OPTIONS required for
 * a single HTTP Verb
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class OptionVerb
{
    static private $instance;

    static public function init(): OptionVerb
    {
        if (self::$instance === null) {
            self::$instance = new OptionVerb();
        }

        return self::$instance;
    }

    static public function setAuthenticationRequired(
        bool $status = false
    ): OptionVerb
    {

        return self::$instance;
    }

    static public function setConditionalParameters(
        array $parameters = []
    ): OptionVerb
    {

        return self::$instance;
    }

    static public function setDescription(
        string $localisation_path
    ): OptionVerb
    {

        return self::$instance;
    }

    static public function setPagination(
        bool $status = false
    ): OptionVerb
    {

        return self::$instance;
    }

    static public function setPaginationOverride(
        bool $status = false
    ): OptionVerb
    {

        return self::$instance;
    }

    static public function setParameters(
        string $config_path
    ): OptionVerb
    {

        return self::$instance;
    }

    static public function setSearchable(
        string $config_path
    ): OptionVerb
    {

        return self::$instance;
    }

    static public function setSortable(
        string $config_path
    ): OptionVerb
    {

        return self::$instance;
    }
}
