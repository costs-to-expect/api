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
        return [];
    }

    public function patchFields(): array // We need post fields and patch fields
    {
        return $this->postFields();
    }

    public function patchValidation(): array // We need to split validation config files
    {
        return [];
    }

    public function postFields(): array // We need post fields and patch fields
    {
        return LaravelConfig::get('api.item-type-allocated-expense.fields');
    }

    public function postValidation(): array // We need to split validation config files
    {
        return [];
    }

    public function requestParameters(): array
    {
        return [];
    }

    public function searchParameters(): array
    {
        return [];
    }

    public function sortParameters(): array
    {
        return [];
    }

    public function summaryFilterParameters(): array
    {
        return [];
    }

    public function summaryRequestParameters(): array
    {
        return [];
    }

    public function summarySearchParameters(): array
    {
        return [];
    }
}
