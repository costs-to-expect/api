<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

/**
 * Helper class to generate the data required to build the OPTIONS required for
 * a single HTTP Verb, in this case GET
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Get
{
    /**
     * @var Get
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
     * @var array
     */
    static private $localised_parameters;

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
        self::$localised_parameters = [];
        self::$pagination = false;
        self::$pagination_parameters = [];
        self::$parameters = [];
        self::$searchable = [];
        self::$searchable_parameters = [];
        self::$sortable_parameters = [];
        self::$sortable = [];
    }

    static public function init(): Get
    {
        if (self::$instance === null) {
            self::$instance = new Get();
            self::$instance->reset();
        }

        return self::$instance;
    }

    static public function setAuthenticationRequired(
        bool $status = false
    ): Get
    {
        self::$authentication = $status;

        return self::$instance;
    }

    static public function setConditionalParameters(
        array $parameters = []
    ): Get
    {
        self::$conditional_parameters = $parameters;

        return self::$instance;
    }

    static public function setDescription(
        string $localisation_path
    ): Get
    {
        self::$description = trans($localisation_path);

        return self::$instance;
    }

    static public function setPagination(
        bool $status = false
    ): Get
    {
        if ($status === true) {
            self::$pagination_parameters = Config::get('api.pagination.parameters');
        }

        return self::$instance;
    }

    static public function setPaginationOverride(
        bool $status = false
    ): Get
    {
        if ($status === true) {
            self::$pagination_parameters = Config::get('api.pagination.parameters-including-collection');
        }

        return self::$instance;
    }

    static public function setParameters(
        string $config_path
    ): Get
    {
        self::$parameters = Config::get($config_path);
        return self::$instance;
    }

    static public function setSearchable(
        string $config_path
    ): Get
    {
        self::$searchable = true;
        self::$searchable_parameters = Config::get($config_path);

        return self::$instance;
    }

    static public function setSortable(
        string $config_path
    ): Get
    {
        self::$sortable = true;
        self::$sortable_parameters = Config::get($config_path);

        return self::$instance;
    }

    static private function buildParameters()
    {
        self::$localised_parameters = [];

        foreach (
            array_merge_recursive(
                self::$pagination_parameters,
                (self::$sortable === true ? Config::get('api.sortable.parameters') : []),
                (self::$searchable === true ? Config::get('api.searchable.parameters') : []),
                self::$parameters,
                self::$conditional_parameters
            )
            as $parameter => $parameter_data
        ) {
            $parameter_data['title'] = trans($parameter_data['title']);
            $parameter_data['description'] = trans($parameter_data['description']);

            self::$localised_parameters[$parameter] = $parameter_data;
        }
    }

    static public function option(): array
    {
        self::buildParameters();

        return [
            'GET' => [
                'description' => self::$description,
                'authentication_required' => self::$authentication,
                'sortable' => self::$sortable_parameters,
                'searchable' => self::$searchable_parameters,
                'parameters' => self::$localised_parameters
            ]
        ];
    }
}
