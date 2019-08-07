<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Resource type model
 *
 * Single() exists in this model to be consistent with all the other models, it is
 * simply a synonym for find()
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType extends Model
{
    protected $table = 'resource_type';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Return the total number of resource types
     *
     * @param boolean $include_private Include private resource types
     * @param array $search_parameters
     *
     * @return integer
     */
    public function totalCount(
        bool $include_private = false,
        array $search_parameters = []
    ): int
    {
        $collection = $this->select("resource_type.id");

        if ($include_private === false) {
            $collection->where('resource_type.private', '=', 0);
        }

        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where('resource_type.' . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        return count($collection->get());
    }

    public function resources()
    {
        return $this->hasMany(Resource::class, 'resource_type_id', 'id');
    }

    /**
     * Return the paginated collection
     *
     * @param boolean $include_private Also include private resource type
     * @param integer $offset Paging offset
     * @param integer $limit Paging limit
     * @param array $search_parameters
     *
     * @return array
     */
    public function paginatedCollection(
        bool $include_private = false,
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = []
    ): array
    {
        $collection = $this->select(
                'resource_type.id AS resource_type_id',
                'resource_type.name AS resource_type_name',
                'resource_type.description AS resource_type_description',
                'resource_type.created_at AS resource_type_created_at',
                'resource_type.private AS resource_type_private'
            )->selectRaw('
                (
                    SELECT 
                        COUNT(resource.id) 
                    FROM 
                        resource 
                    WHERE 
                        resource.resource_type_id = resource_type.id
                ) AS resource_type_resources'
            )->
            leftJoin("resource", "resource_type.id", "resource.id")->
            orderByDesc('resource_type.created_at');

        if ($include_private === false) {
            $collection->where('private', '=', 0);
        }

        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where('resource_type.' . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        $collection->offset($offset);
        $collection->limit($limit);

        return $collection->get()->toArray();
    }

    /**
     * Return a single item
     *
     * @param integer $resource_type_id Resource type to return
     * @param boolean $include_private Add additional check to ensure we don't return private resource types
     *
     * @return array
     */
    public function single(
        int $resource_type_id,
        bool $include_private = false
    ): array
    {
        $result = $this->select(
                'resource_type.id AS resource_type_id',
                'resource_type.name AS resource_type_name',
                'resource_type.description AS resource_type_description',
                'resource_type.created_at AS resource_type_created_at',
                'resource_type.private AS resource_type_private'
            )->selectRaw('
                (
                    SELECT 
                        COUNT(resource.id) 
                    FROM 
                        resource 
                    WHERE 
                        resource.resource_type_id = resource_type.id
                ) AS resource_type_resources'
            )->
            leftJoin("resource", "resource_type.id", "resource.id");

        if ($include_private === false) {
            $result->where('resource_type.private', '=', 0);
        }

        return $result->find($resource_type_id)->
            toArray();
    }

    /**
     * Return the an minimised collection, typically to be used in OPTIONS
     *
     * @param boolean $include_private
     *
     * @return array
     */
    public function minimisedCollection(
        bool $include_private = false
    ): array
    {
        $collection = $this->orderBy('resource_type.name')
            ->select(
                'resource_type.id AS resource_type_id',
                'resource_type.name AS resource_type_name',
                'resource_type.description AS resource_type_description'
            );

        if ($include_private === false) {
            $collection->where('private', '=', 0);
        }

        return $collection->get()->
            toArray();
    }

    /**
     * Convert the model instance to an array for use with the transformer
     *
     * @param ResourceType
     *
     * @return array
     */
    public function instanceToArray(ResourceType $resource_type): array
    {
        return [
            'resource_type_id' => $resource_type->id,
            'resource_type_name' => $resource_type->name,
            'resource_type_description' => $resource_type->description,
            'resource_type_created_at' => $resource_type->created_at->toDateTimeString(),
            'resource_type_private' => $resource_type->private
        ];
    }
}
