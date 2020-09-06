<?php
declare(strict_types=1);

namespace App\Entity;

use App\Models\Transformers\Transformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config as LaravelConfig;

/**
 @todo We will need a console command to create the config files for a new type
 * Add the ticket to Pivotal when/if happy with this.
 */

abstract class Config
{
    protected string $base_path;

    public function __construct()
    {
        //
    }

    public function dateRangeField(): ?string
    {
        return null;
    }

    public function filterParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.filterable', []);
    }

    public function itemRequestParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.parameters.item', []); // We need to split this
    }

    abstract public function model(): Model; // @todo We need to update this to a better model

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

    public function searchParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.searchable', []);
    }

    public function sortParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.sortable', []);
    }

    public function summaryFilterParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.summary-filterable', []);
    }

    public function summaryRequestParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.summary-parameters', []);
    }

    public function summarySearchParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.summary-searchable', []);
    }

    abstract public function table(): string;

    abstract public function transformer(array $data_to_transform): Transformer;

    abstract public function type(): string;
}
