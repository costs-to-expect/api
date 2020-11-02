<?php
declare(strict_types=1);

namespace App\Entity\Item;

use App\Models\Transformers\Transformer;
use App\Request\Parameter\Request;
use App\Request\Validate\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config as LaravelConfig;

/**
 @todo We will need a console command to create the config files for a new type
 * Add the ticket to Pivotal when/if happy with this.
 */

abstract class Item
{
    protected string $base_path;

    protected string $resource_type_base_path;

    public function __construct()
    {
        //
    }

    abstract protected function allowedValuesItemCollectionClass(): string;

    public function allowedValuesForItemCollection(
        int $resource_type_id,
        int $resource_id,
        array $permitted_resource_types = [],
        bool $include_public = false
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
            $permitted_resource_types,
            $include_public,
        );

        return $allowed_values
            ->setParameters(
                $available_parameters,
                $defined_parameters
            )
            ->fetch()
            ->allowedValues();
    }

    public function categoryAssignmentLimit(): int
    {
        return 1;
    }

    abstract public function create(int $id): Model;

    public function dateRangeField(): ?string
    {
        return null;
    }

    public function filterParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.filterable', []);
    }

    abstract public function instance(int $id): Model;

    public function itemRequestParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.parameters.item', []); // We need to split this
    }

    abstract public function model();

    public function patchFields(): array // We need post fields and patch fields
    {
        return $this->postFields();
    }

    public function patchValidation(): array // We need to split validation config files
    {
        return LaravelConfig::get($this->base_path . '.validation.PATCH.fields', []);
    }

    public function patchValidationMessages(): array // We need to split validation config files
    {
        return LaravelConfig::get($this->base_path . '.validation.PATCH.messages', []);
    }

    public function postFields(): array // We need post fields and patch fields
    {
        return LaravelConfig::get($this->base_path . '.fields', []);
    }

    public function postValidation(): array // We need to split validation config files
    {
        return LaravelConfig::get($this->base_path . '.validation.POST.fields', []);
    }

    public function postValidationMessages(): array // We need to split validation config files
    {
        return LaravelConfig::get($this->base_path . '.validation.POST.messages', []);
    }

    public function requestParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.parameters.collection', []); // We need to split this
    }

    public function resourceTypeFilterParameters(): array
    {
        return LaravelConfig::get($this->resource_type_base_path . '.filterable', []);
    }

    abstract public function resourceTypeModel(): Model;

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

    abstract public function resourceTypeTransformer(array $data_to_transform): Transformer;

    public function searchParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.searchable', []);
    }

    public function sortParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.sortable', []);
    }

    public function subcategoryAssignmentLimit(): int
    {
        return 1;
    }

    public function summaryFilterParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.summary-filterable', []);
    }

    abstract public function summaryModel(): Model;

    public function summaryRequestParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.summary-parameters', []);
    }

    public function summaryResourceTypeFilterParameters(): array
    {
        return LaravelConfig::get($this->resource_type_base_path . '.summary-filterable', []);
    }

    abstract public function summaryResourceTypeModel(): Model;

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

    abstract public function summaryTransformer(array $data_to_transform): Transformer;

    abstract public function summaryTransformerByCategory(array $data_to_transform): Transformer;

    abstract public function summaryTransformerBySubcategory(array $data_to_transform): Transformer;

    abstract public function summaryTransformerByYear(array $data_to_transform): Transformer;

    abstract public function summaryTransformerByMonth(array $data_to_transform): Transformer;

    abstract public function summaryTransformerByResource(array $data_to_transform): Transformer;

    abstract public function table(): string;

    abstract public function transformer(array $data_to_transform): Transformer;

    abstract public function type(): string;

    abstract public function update(array $patch, Model $instance): bool;

    abstract public function validator(): Validator;
}
