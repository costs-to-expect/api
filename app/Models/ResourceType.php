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

    public function numberOfResources()
    {
        return $this->hasMany(Resource::class, 'resource_type_id', 'id')->count();
    }

    public function paginatedCollection(int $offset = 0, int $limit = 10)
    {
        return $this->latest()->all();
    }

    public function single(int $resource_type_id)
    {
        return $this->find($resource_type_id);
    }
}
