<?php
declare(strict_types=1);

namespace App\Entity;

use Illuminate\Support\Facades\Config as LaravelConfig;

/**
 @todo We will need a console command to create the config files for a new type
 * Add the ticket to Pivotal when/if happy with this.
 */

class Config
{
    protected string $base_path;

    public function __construct()
    {

    }

    public function filterParameters(): array
    {
        return LaravelConfig::get($this->base_path . '.filterable', []);
    }

    public function itemParameters(): array
    {
        return [];
    }

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
        return LaravelConfig::get($this->base_path . '.parameters', []);
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
}
