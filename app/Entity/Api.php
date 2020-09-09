<?php
declare(strict_types=1);

namespace App\Entity;

use Illuminate\Support\Facades\Config as LaravelConfig;

class Api
{
    public function __construct()
    {

    }

    public function filterParameter(): array
    {
        return LaravelConfig::get('api.app.filterable-parameters', []);
    }

    public function paginationParameters(): array
    {
        return LaravelConfig::get('api.app.pagination-parameters', []);
    }

    public function paginationParametersAllowingEntireCollection(): array
    {
        return LaravelConfig::get('api.app.pagination-parameters-including-collection', []);
    }

    public function searchParameter(): array
    {
        return LaravelConfig::get('api.app.searchable-parameters', []);
    }

    public function sortParameter(): array
    {
        return LaravelConfig::get('api.app.sortable-parameters', []);
    }
}
