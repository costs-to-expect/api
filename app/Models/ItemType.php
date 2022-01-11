<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Item type model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemType extends Model
{
    protected $table = 'item_type';

    protected $guarded = ['id', 'name', 'description', 'example', 'created_at', 'updated_at'];

    /**
     * Fetch the item types, id, name and description only
     *
     * @return array
     */
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

    /**
     * Return the paginated collection
     *
     * @param integer $offset Paging offset
     * @param integer $limit Paging limit
     * @param array $search_parameters
     * @param array $sort_parameters
     *
     * @return array
     */
    public function paginatedCollection(
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = [],
        array $sort_parameters = []
    ): array
    {
        $collection = $this->select(
            'item_type.id AS item_type_id',
            'item_type.name AS item_type_name',
            'item_type.friendly_name AS item_type_friendly_name',
            'item_type.description AS item_type_description',
            'item_type.example AS item_type_example',
            'item_type.created_at AS item_type_created_at'
        );

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy('item_type.created_at', $direction);
                        break;

                    default:
                        $collection->orderBy('item_type.' . $field, $direction);
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

    /**
     * Return the total number of item types
     *
     * @param array $search_parameters = []
     *
     * @return integer
     */
    public function totalCount(array $search_parameters = []): int
    {
        $collection = $this->select("item_type.id");

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

        return $collection->count();
    }
}
