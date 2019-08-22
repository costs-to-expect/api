<?php
declare(strict_types=1);

namespace App\Models;

use App\Utilities\Model as ModelUtility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Resource model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Resource extends Model
{
    protected $table = 'resource';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Return the total number of resources
     *
     * @param integer $resource_type_id
     * @param boolean $include_private Include resources attached to private resource types
     * @param array $search_parameters
     *
     * @return integer
     */
    public function totalCount(
        int $resource_type_id,
        bool $include_private = false,
        array $search_parameters = []
    ): int
    {
        $collection = $this->select("resource.id")->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        if ($include_private === false) {
            $collection->where('resource_type.private', '=', 0);
        }

        $collection = ModelUtility::applySearch($collection, $this->table, $search_parameters);

        return count($collection->get());
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
        $collection = $this->select(
                'resource.id AS resource_id',
                'resource.name AS resource_name',
                'resource.description AS resource_description',
                'resource.effective_date AS resource_effective_date',
                'resource.created_at AS resource_created_at'
            )->
            where('resource_type_id', '=', $resource_type_id);

        $collection = ModelUtility::applySearch($collection, $this->table, $search_parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy('created_at', $direction);
                        break;

                    default:
                        $collection->orderBy($field, $direction);
                        break;
                }
            }
        } else {
            $collection->latest();
        }

        return $collection->offset($offset)->
            limit($limit)->
            get()->
            toArray();
    }

    public function single(int $resource_type_id, int $resource_id): ?array
    {
        $result = $this->select(
                'resource.id AS resource_id',
                'resource.name AS resource_name',
                'resource.description AS resource_description',
                'resource.effective_date AS resource_effective_date',
                'resource.created_at AS resource_created_at'
            )->
            where('resource_type_id', '=', $resource_type_id)->
            find($resource_id);

        if ($result !== null) {
            return $result->toArray();
        } else {
            return null;
        }
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

    /**
     * Convert the model instance to an array for use with the transformer
     *
     * @param Resource
     *
     * @return array
     */
    public function instanceToArray(Resource $resource): array
    {
        return [
            'resource_id' => $resource->id,
            'resource_name' => $resource->name,
            'resource_description' => $resource->description,
            'resource_effective_date' => $resource->effective_date,
            'resource_created_at' => $resource->created_at->toDateTimeString()
        ];
    }
}
