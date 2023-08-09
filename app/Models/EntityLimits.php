<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class EntityLimits extends Model
{
    public function maximumYearByResourceType(
        int $resource_type_id,
        string $table,
        string $field
    ): int {
        return $this->yearByResourceType(
            $resource_type_id,
            $table,
            $field,
            'MAX'
        );
    }

    public function minimumYearByResourceType(
        int $resource_type_id,
        string $table,
        string $field
    ): int {
        return $this->yearByResourceType(
            $resource_type_id,
            $table,
            $field,
            'MIN'
        );
    }

    public function maximumYearByResourceTypeAndResource(
        int $resource_type_id,
        int $resource_id,
        string $table,
        string $field
    ): int {
        return $this->yearByResourceTypeAndResource(
            $resource_type_id,
            $resource_id,
            $table,
            $field,
            'MAX'
        );
    }

    public function minimumYearByResourceTypeAndResource(
        int $resource_type_id,
        int $resource_id,
        string $table,
        string $field
    ): int {
        return $this->yearByResourceTypeAndResource(
            $resource_type_id,
            $resource_id,
            $table,
            $field,
            'MIN'
        );
    }

    private function yearByResourceType(
        int $resource_type_id,
        string $table,
        string $field,
        string $aggregate
    ): int {
        $result = $this->from($table)
            ->join('item', $table . '.item_id', 'item.id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->where('resource.resource_type_id', '=', $resource_type_id)
            ->selectRaw('YEAR(' . $aggregate . '(`' . $table . '`.`' . $field . '`)) AS `date_limit`')
            ->first();

        if ($result !== null) {
            return (int) $result->date_limit;
        }

        return (int) date('Y');
    }

    private function yearByResourceTypeAndResource(
        int $resource_type_id,
        int $resource_id,
        string $table,
        string $field,
        string $aggregate
    ): int {
        $result = $this->from($table)
            ->join('item', $table . '.item_id', 'item.id')
            ->join('resource', 'item.resource_id', 'resource.id')
            ->where('resource.resource_type_id', '=', $resource_type_id)
            ->where('item.resource_id', '=', $resource_id)
            ->selectRaw('YEAR(' . $aggregate . '(`' . $table . '`.`' . $field . '`)) AS `date_limit`')
            ->first();

        if ($result !== null) {
            return (int) $result->date_limit;
        }

        return (int) date('Y');
    }
}
