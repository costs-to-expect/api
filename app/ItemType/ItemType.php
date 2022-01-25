<?php
declare(strict_types=1);

namespace App\ItemType;

use App\Transformers\Transformer;
use App\Request\Parameter\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config as LaravelConfig;

abstract class ItemType
{
    protected string $base_path;

    protected string $resource_type_base_path;

    public function __construct()
    {
        //
    }

    abstract public function allowedValuesForItem(int $resource_type_id): array;

    public function allowedValuesForItemCollection(
        int $resource_type_id,
        int $resource_id,
        array $viewable_resource_types = []
    ): array
    {
        $available_parameters = $this->requestParameters();
        $defined_parameters = Request::fetch(
            array_keys($available_parameters),
            $resource_type_id,
            $resource_id
        );

        $allowed_value_class = $this->allowedValuesItemCollectionClass();
        $allowed_values = new $allowed_value_class(
            $resource_type_id,
            $resource_id,
            $viewable_resource_types
        );

        return $allowed_values
            ->setParameters(
                $available_parameters,
                $defined_parameters
            )
            ->fetch()
            ->allowedValues();
    }

    public function allowedValuesForResourceTypeItemCollection(
        int $resource_type_id,
        array $viewable_resource_types = []
    ): array
    {
        $available_parameters = $this->resourceTypeRequestParameters();
        $defined_parameters = Request::fetch(
            array_keys($available_parameters),
            $resource_type_id
        );

        $allowed_value_class = $this->allowedValuesResourceTypeItemCollectionClass();
        $allowed_values = new $allowed_value_class(
            $resource_type_id,
            $viewable_resource_types
        );

        return $allowed_values->setParameters(
                $available_parameters,
                $defined_parameters
            )
            ->fetch()
            ->allowedValues();
    }

    public function allowPartialTransfers(): bool
    {
        return false;
    }

    abstract public function create(int $id): Model;

    public function dateRangeField(): ?string
    {
        return null;
    }

    abstract public function instance(int $id): Model;

    public function requestParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.parameters.collection', []); // We need to split this
    }

    public function resourceTypeFilterParameters(): array
    {
        return LaravelConfig::get($this->resource_type_base_path . '.filterable', []);
    }

    public function resourceTypeRequestParameters(): array
    {
        return LaravelConfig::get($this->resource_type_base_path . '.parameters.collection', []);
    }

    public function resourceTypeSearchParameters(): array
    {
        return LaravelConfig::get($this->resource_type_base_path . '.searchable', []);
    }

    public function resourceTypeSortParameters(): array
    {
        return LaravelConfig::get($this->resource_type_base_path . '.sortable', []);
    }

    public function summaryFilterParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.summary-filterable', []);
    }

    public function summaryRequestParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.summary-parameters', []);
    }

    public function summaryResourceTypeFilterParameters(): array
    {
        return LaravelConfig::get($this->resource_type_base_path . '.summary-filterable', []);
    }

    public function summaryResourceTypeRequestParameters(): array
    {
        return LaravelConfig::get($this->resource_type_base_path . '.summary-parameters', []);
    }

    public function summaryResourceTypeSearchParameters(): array
    {
        return LaravelConfig::get($this->resource_type_base_path . '.summary-searchable', []);
    }

    public function summarySearchParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.summary-searchable', []);
    }

    abstract public function table(): string;

    abstract public function transformer(array $data_to_transform): Transformer;

    abstract public function type(): string;

    abstract public function update(array $patch, Model $instance): bool;

    abstract protected function allowedValuesItemCollectionClass(): string;
    abstract protected function allowedValuesResourceTypeItemCollectionClass(): string;
}
