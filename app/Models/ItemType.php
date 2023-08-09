<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin QueryBuilder
 *
 * @property int $id
 * @property string $name
 * @property string $friendly_name
 * @property string $description
 * @property string $example
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemType extends Model
{
    protected $table = 'item_type';

    protected $guarded = ['id', 'name', 'description', 'example', 'created_at', 'updated_at'];

    public function minimisedCollection(): array
    {
        return $this->select(
            'item_type.id AS item_type_id',
            'item_type.name AS item_type_name',
            'item_type.description AS item_type_description'
        )->
            get()->
            toArray();
    }

    public function paginatedCollection(
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = [],
        array $sort_parameters = []
    ): array {
        $collection = $this->select(
            'item_type.id AS item_type_id',
            'item_type.name AS item_type_name',
            'item_type.friendly_name AS item_type_friendly_name',
            'item_type.description AS item_type_description',
            'item_type.example AS item_type_example',
            'item_type.created_at AS item_type_created_at'
        );

        $collection = Utility::applySearchClauses($collection, $this->table, $search_parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy($this->table . '.created_at', $direction);
                        break;

                    default:
                        $collection->orderBy($this->table . '.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderByDesc('item_type.created_at');
        }

        $collection->offset($offset);
        $collection->limit($limit);

        return $collection->get()->toArray();
    }

    public function single(int $item_type_id): ?array
    {
        $result = $this
            ->select(
                'item_type.id AS item_type_id',
                'item_type.name AS item_type_name',
                'item_type.friendly_name AS item_type_friendly_name',
                'item_type.description AS item_type_description',
                'item_type.example AS item_type_example',
                'item_type.created_at AS item_type_created_at'
            );

        $result = $result
            ->where($this->table . '.id', '=', $item_type_id)
            ->get()
            ->toArray();

        if (count($result) === 0) {
            return null;
        }

        return $result[0];
    }

    public function totalCount(array $search_parameters = []): int
    {
        $collection = $this->select("item_type.id");

        $collection = Utility::applySearchClauses($collection, $this->table, $search_parameters);

        return $collection->count();
    }
}
