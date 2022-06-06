<?php
declare(strict_types=1);

namespace App\ItemType;

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

    abstract public function create(int $id): Model;

    abstract public function instance(int $id): Model;

    public function requestParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.parameters', []); // We need to split this
    }

    public function resourceTypeFilterParameters(): array
    {
        return LaravelConfig::get($this->resource_type_base_path . '.filterable', []);
    }

    public function resourceTypeRequestParameters(): array
    {
        return LaravelConfig::get($this->resource_type_base_path . '.parameters', []);
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

    abstract public function type(): string;

    abstract public function update(array $patch, Model $instance): bool;
}
