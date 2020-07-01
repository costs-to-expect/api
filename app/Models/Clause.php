<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Model helper class
 *
 * As with all utility classes, eventually they may be moved into libraries if
 * they gain more than a few functions and the creation of a library makes
 * sense.
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Clause
{
    public static function applySearch(
        $collection,
        string $table,
        array $search_parameters = []
    )
    {
        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where($table . '.' . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        return $collection;
    }

    public static function applyFiltering(
        $collection,
        string $table,
        array $filter_parameters = []
    )
    {
        if (count($filter_parameters) > 0) {
            foreach ($filter_parameters as $field => $range) {
                $collection->where($table . '.' . $field, '>=', $range['from']);
                $collection->where($table . '.' . $field, '<=', $range['to']);
            }
        }

        return $collection;
    }

    public static function applyResourceTypeCollectionCondition(
        $collection,
        array $permitted_resource_types,
        bool $include_public
    )
    {
        if ($include_public === true) {
            $collection->where(static function ($collection) use ($permitted_resource_types, $include_public) {
                $collection->where('resource_type.public', '=', (int) $include_public)->
                    orWhereIn('resource_type.id', $permitted_resource_types);
            });
        } else {
            $collection->WhereIn('resource_type.id', $permitted_resource_types);
        }

        return $collection;
    }
}
