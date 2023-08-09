<?php

declare(strict_types=1);

namespace App\HttpVerb;

use Illuminate\Support\Facades\Config as LaravelConfig;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Get extends Verb
{
    protected bool $pagination;
    protected array $pagination_parameters;

    protected array $parameters;
    protected array $dynamic_parameters;
    protected array $parameters_after_localisation;

    protected bool $filterable;
    protected array $filterable_parameters;

    protected bool $searchable;
    protected array $searchable_parameters;

    protected bool $sortable;
    protected array $sortable_parameters;

    public function __construct()
    {
        parent::__construct();

        $this->pagination = false;
        $this->pagination_parameters = [];

        $this->parameters = [];
        $this->dynamic_parameters = [];
        $this->parameters_after_localisation = [];

        $this->filterable = false;
        $this->filterable_parameters = [];

        $this->searchable = false;
        $this->searchable_parameters = [];

        $this->sortable = false;
        $this->sortable_parameters = [];
    }

    public function setFilterableParameters(
        array $parameters
    ): Get {
        if (count($parameters) > 0) {
            $this->filterable = true;
            $this->filterable_parameters = $parameters;
        }

        return $this;
    }

    public function setPaginationStatus(
        bool $status = false,
        bool $override = false
    ): Get {
        if ($status === true) {
            $this->pagination = true;

            if ($override === false) {
                $this->pagination_parameters = $this->paginationParameters();
            } else {
                $this->pagination_parameters = $this->paginationParametersAllowingEntireCollection();
            }
        }

        return $this;
    }

    public function setParameters(
        array $parameters
    ): Get {
        if (count($parameters) > 0) {
            $this->parameters = $parameters;
        }

        return $this;
    }

    public function setAllowedValuesForParameters(
        array $parameters = []
    ): Get {
        $this->dynamic_parameters = $parameters;

        return $this;
    }

    public function setSearchableParameters(
        array $parameters
    ): Get {
        if (count($parameters) > 0) {
            $this->searchable = true;
            $this->searchable_parameters = $parameters;
        }

        return $this;
    }

    public function setSortableParameters(
        array $parameters
    ): Get {
        if (count($parameters) > 0) {
            $this->sortable = true;
            $this->sortable_parameters = $parameters;
        }

        return $this;
    }

    protected function mergeAndLocalise(): void
    {
        foreach (
            array_merge_recursive(
                $this->pagination_parameters,
                ($this->sortable === true ? $this->sortParameter() : []),
                ($this->searchable === true ? $this->searchParameter() : []),
                ($this->filterable === true ? $this->filterParameter() : []),
                $this->parameters,
                $this->dynamic_parameters
            )
            as $parameter => $parameter_data
        ) {
            if (
                array_key_exists('title', $parameter_data) === true &&
                array_key_exists('description', $parameter_data) === true
            ) {
                $parameter_data['title'] = trans($parameter_data['title']);
                $parameter_data['description'] = trans($parameter_data['description']);

                $this->parameters_after_localisation[$parameter] = $parameter_data;
            }
        }
    }

    public function option(): array
    {
        $this->mergeAndLocalise();

        return [
            'description' => $this->description,
            'authentication' => [
                'required' => $this->authentication,
                'authenticated' => $this->authenticated
            ],
            'sortable' => $this->sortable_parameters,
            'searchable' => $this->searchable_parameters,
            'filterable' => $this->filterable_parameters,
            'parameters' => $this->parameters_after_localisation
        ];
    }

    protected function filterParameter(): array
    {
        return LaravelConfig::get('api.app.filterable-parameters', []);
    }

    protected function paginationParameters(): array
    {
        return LaravelConfig::get('api.app.pagination-parameters', []);
    }

    protected function paginationParametersAllowingEntireCollection(): array
    {
        return LaravelConfig::get('api.app.pagination-parameters-including-collection', []);
    }

    protected function searchParameter(): array
    {
        return LaravelConfig::get('api.app.searchable-parameters', []);
    }

    protected function sortParameter(): array
    {
        return LaravelConfig::get('api.app.sortable-parameters', []);
    }
}
