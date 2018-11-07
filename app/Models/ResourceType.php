<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Resource type model
 *
 * Single() exists in this model to be consistent with all the other models, it is
 * simply a synonym for find()
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType extends Model
{
    protected $table = 'resource_type';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function resources()
    {
        return $this->hasMany(Resource::class, 'resource_type_id', 'id');
    }

    public function resources_count()
    {
        return $this->hasMany(Resource::class, 'resource_type_id', 'id')->count();
    }

    /**
     * Return the paginated collection
     *
     * @param boolean $include_private Also include private resource type
     * @param integer $offset Paging offset
     * @param integer $limit Paging limit
     * @return mixed
     */
    public function paginatedCollection(bool $include_private, int $offset = 0, int $limit = 10)
    {
        $collection = $this->latest();

        if ($include_private === false) {
            $collection->where('private', '=', 0);
        }

        return $collection->get();
    }

    /**
     * Return a single item
     *
     * @param integer $resource_type_id Resource type to return
     * @param boolean $include_private Add additional check to ensure we don't return private resource types
     * @return mixed
     */
    public function single(int $resource_type_id, bool $include_private)
    {
        if ($include_private === false) {
            return $this->where('private', '=', 0)->find($resource_type_id);
        } else {
            return $this->find($resource_type_id);
        }
    }

    /**
     * Return the an minimised collection, typically to be used in OPTIONS
     *
     * @return \Illuminate\Support\Collection
     */
    public function minimisedCollection()
    {
        return $this->orderBy('resource_type.name')
            ->select(
                'resource_type.id AS resource_type_id',
                'resource_type.name AS resource_type_name',
                'resource_type.description AS resource_type_description'
            )
            ->get();
    }
}
