<?php
declare(strict_types=1);

namespace App\Utilities;

use Illuminate\Support\Facades\Config;

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
    /**
     * @var OptionGet
     */
    static private $instance;

    /**
     * @var boolean
     */
    static private $authentication;

    /**
     * @var array
     */
    static private $conditional_parameters;

    /**
     * @var string
     */
    static private $description;

    /**
     * @var boolean
     */
    static private $pagination;

    /**
     * @var array
     */
    static private $pagination_parameters;

    /**
     * @var array
     */
    static private $parameters;

    /**
     * @var array
     */
    static private $searchable;

    /**
     * @var array
     */
    static private $searchable_parameters;

    /**
     * @var array
     */
    static private $sortable_parameters;

    /**
     * @var array
     */
    static private $sortable;

    static private function reset()
    {
        self::$authentication = false;
        self::$conditional_parameters = [];
        self::$description = null;
        self::$pagination = false;
        self::$pagination_parameters = [];
        self::$parameters = [];
        self::$searchable = [];
        self::$searchable_parameters = [];
        self::$sortable_parameters = [];
        self::$sortable = [];
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
        if ($status === true) {
            self::$pagination_parameters = Config::get('api.pagination.parameters');
        }

        return self::$instance;
    }

    static public function setPaginationOverride(
        bool $status = false
    ): OptionGet
    {
        if ($status === true) {
            self::$pagination_parameters = Config::get('api.pagination.parameters-including-collection');
        }

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
        self::$searchable = true;
        self::$searchable_parameters = Config::get($config_path);

        return self::$instance;
    }

    static public function setSortable(
        string $config_path
    ): OptionGet
    {
        self::$sortable = true;
        self::$sortable_parameters = Config::get($config_path);

        return self::$instance;
    }

    static public function option(): array
    {
        return [
            'GET' => [
                'description' => self::$description,
                'authentication_required' => self::$authentication,
                'sortable' => self::$sortable,
                'searchable' => self::$searchable,
                'parameters' => array_merge_recursive(
                    self::$pagination_parameters,
                    self::$sortable_parameters,
                    self::$searchable_parameters
                )
            ]
        ];
    }
}
