<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Config;

/**
 * Resource model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Resource extends Model
{
    protected $table = 'resource';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function item_subtype()
    {
        return $this->hasOneThrough(
            ItemSubtype::class,
            ResourceItemSubtype::class,
            'resource_id',
            'id',
            null,
            'item_subtype_id'
        );
    }

    /**
     * Return an array of the fields that can be PATCHed.
     *
     * @return array
     */
    public function patchableFields(): array
    {
        return array_keys(Config::get('api.resource.validation.PATCH.fields'));
    }

    public function totalCount(
        int $resource_type_id,
        array $viewable_resource_types,
        array $search_parameters = []
    ): int
    {
        $collection = $this
            ->select("resource.id")
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->where('resource_type.id', '=', $resource_type_id);

        $collection = Clause::applyViewableResourceTypes(
            $collection,
            $viewable_resource_types
        );

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

        return $collection->count();
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'resource_id', 'id');
    }

    public function resource_type()
    {
        return $this->belongsTo(ResourceType::class, 'resource_type_id', 'id');
    }

    public function paginatedCollection(
        int $resource_type_id,
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = [],
        array $sort_parameters = []
    ): array
    {
        $collection = $this
            ->select(
                'resource.id AS resource_id',
                'resource.name AS resource_name',
                'resource.description AS resource_description',
                'resource.effective_date AS resource_effective_date',
                'resource.created_at AS resource_created_at',
                'item_subtype.id AS resource_item_subtype_id',
                'item_subtype.name AS resource_item_subtype_name',
                'item_subtype.description AS resource_item_subtype_description'
            )
            ->join('resource_item_subtype', 'resource_item_subtype.resource_id', 'resource.id')
            ->join('item_subtype', 'resource_item_subtype.item_subtype_id', 'item_subtype.id')
            ->where('resource_type_id', '=', $resource_type_id);

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy('resource.created_at', $direction);
                        break;

                    default:
                        $collection->orderBy('resource.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderBy('resource.created_at', 'desc');
        }

        return $collection->offset($offset)->
            limit($limit)->
            get()->
            toArray();
    }

    public function single(int $resource_type_id, int $resource_id): ?array
    {
        $result = $this
            ->select(
                'resource.id AS resource_id',
                'resource.name AS resource_name',
                'resource.description AS resource_description',
                'resource.effective_date AS resource_effective_date',
                'resource.created_at AS resource_created_at',
                'item_subtype.id AS resource_item_subtype_id',
                'item_subtype.name AS resource_item_subtype_name',
                'item_subtype.description AS resource_item_subtype_description'
            )
            ->join('resource_item_subtype', 'resource_item_subtype.resource_id', 'resource.id')
            ->join('item_subtype', 'resource_item_subtype.item_subtype_id', 'item_subtype.id')
            ->where('resource_type_id', '=', $resource_type_id);

        $result = $result
            ->where($this->table . '.id', '=', $resource_id)
            ->get()
            ->toArray();

        if (count($result) === 0) {
            return null;
        }

        return $result[0];
    }

    /**
     * Return the list of resources for the requested resource type and
     * optionally exclude the provided resource id
     *
     * @param integer $resource_type_id
     * @param integer|null $exclude_id
     *
     * @return array
     */
    public function resourcesForResourceType(
        int $resource_type_id,
        int $exclude_id = null
    ): array
    {
        $collection = $this->where('resource_type_id', '=', $resource_type_id);

        if ($exclude_id !== null) {
            $collection->where('id', '!=', $exclude_id);
        }

        return $collection->select(
                'resource.id AS resource_id',
                'resource.name AS resource_name',
                'resource.description AS resource_description'
            )->
            get()->
            toArray();
    }

    public function instanceToArray(Model $resource): array
    {
        return [
            'resource_id' => $resource->id,
            'resource_name' => $resource->name,
            'resource_description' => $resource->description,
            'resource_effective_date' => $resource->effective_date,
            'resource_created_at' => $resource->created_at->toDateTimeString(),
            'resource_item_subtype_id' => $resource->item_subtype->id,
            'resource_item_subtype_name' => $resource->item_subtype->name,
            'resource_item_subtype_description' => $resource->item_subtype->description
        ];
    }

    public function instance(
        int $resource_type_id,
        int $resource_id
    ): ?Model
    {
        return $this
            ->select(
                'resource.id',
                'resource.name',
                'resource.description',
                'resource.effective_date'
            )
            ->where('resource_type_id', '=', $resource_type_id)
            ->where('id', '=', $resource_id)
            ->first();
    }
}
