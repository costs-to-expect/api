<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Config;

/**
 * @mixin QueryBuilder
 *
 * @property int $id
 * @property int $resource_type_id
 * @property string $name
 * @property string $description
 * @property string $data
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Resource extends Model
{
    protected $table = 'resource';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function item_subtype(): HasOneThrough
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

    public function patchableFields(): array
    {
        return array_keys(Config::get('api.resource.validation-patch.fields'));
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

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'resource_id', 'id');
    }

    public function resource_type(): BelongsTo
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
                'resource.data AS resource_data',
                'resource.created_at AS resource_created_at',
                'item_subtype.id AS resource_item_subtype_id',
                'item_subtype.name AS resource_item_subtype_name',
                'item_subtype.description AS resource_item_subtype_description'
            )
            ->selectRaw('
                (
                    SELECT 
                        GREATEST(
                            MAX(resource.created_at), 
                            IFNULL(MAX(resource.updated_at), 0),
                            0
                        )
                    FROM 
                        resource
                    WHERE
                        `resource_type_id` = ? 
                ) AS last_updated',
                [
                    $resource_type_id
                ]
            )
            ->join('resource_item_subtype', 'resource_item_subtype.resource_id', 'resource.id')
            ->join('item_subtype', 'resource_item_subtype.item_subtype_id', 'item_subtype.id')
            ->where('resource_type_id', '=', $resource_type_id);

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

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
            $collection->orderBy($this->table . '.created_at', 'desc');
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
                'resource.data AS resource_data',
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
                'resource.description AS resource_description',
                'resource.data AS resource_data'
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
            'resource_data' => $resource->data,
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
                'resource.data'
            )
            ->where('resource_type_id', '=', $resource_type_id)
            ->where('id', '=', $resource_id)
            ->first();
    }
}
