<?php
declare(strict_types=1);

namespace App\Models;

use App\Utilities\Model as ModelUtility;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Item type model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemType extends Model
{
    protected $table = 'item_type';

    protected $guarded = ['id', 'name', 'description', 'created_at', 'updated_at'];

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
            'item_type.description AS item_type_description',
            'item_type.created_at AS item_type_created_at'
        );

        $collection = ModelUtility::applySearch($collection, $this->table, $search_parameters);

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

    /**
     * Return a single item type
     *
     * @param integer $item_type_id
     *
     * @return array
     */
    public function single(int $item_type_id): array
    {
        $result = $this->select(
            'item_type.id AS item_type_id',
            'item_type.name AS item_type_name',
            'item_type.description AS item_type_description',
            'item_type.created_at AS item_type_created_at'
        );

        return $result->find($item_type_id)->
            toArray();
    }

    /**
     * Validate that the item type exists
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function existsToUser(int $id): bool
    {
        $collection = $this->where('item_type.id', '=', $id);

        return (count($collection->get()) === 1) ? true : false;
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

        $collection = ModelUtility::applySearch($collection, $this->table, $search_parameters);

        return $collection->count();
    }
}
