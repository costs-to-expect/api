<?php
declare(strict_types=1);

namespace App\Utilities;

/**
 * Helper class to generate the data required to build the OPTIONS required for
 * a single HTTP Verb, in this case GET
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class OptionGet
{
    static private $instance;
    static private $option;

    static private function reset()
    {
        // Clear any values, reset them
    }

    static public function init(): OptionGet
    {
        if (self::$instance === null) {
            self::$instance = new OptionGet();
            self::$instance->reset();
        }

        return self::$instance;
    }

    static public function setAuthenticationRequired(
        bool $status = false
    ): OptionGet
    {

        return self::$instance;
    }

    static public function setConditionalParameters(
        array $parameters = []
    ): OptionGet
    {

        return self::$instance;
    }

    static public function setDescription(
        string $localisation_path
    ): OptionGet
    {

        return self::$instance;
    }

    static public function setPagination(
        bool $status = false
    ): OptionGet
    {

        return self::$instance;
    }

    static public function setPaginationOverride(
        bool $status = false
    ): OptionGet
    {

        return self::$instance;
    }

    static public function setParameters(
        string $config_path
    ): OptionGet
    {

        return self::$instance;
    }

    static public function setSearchable(
        string $config_path
    ): OptionGet
    {

        return self::$instance;
    }

    static public function setSortable(
        string $config_path
    ): OptionGet
    {

        return self::$instance;
    }

    static public function option(): array
    {
        return [
            'GET' => [
                'description' => null, // These values should come from properties
                'authentication_required' => false, // All default to sensible values
                'sortable' => false, // Base class is responsible for working out
                'searchable' => false, // what null and empty arrays mean
                'parameters' => [] // etc
            ]
        ];
    }
}
