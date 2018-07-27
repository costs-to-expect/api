<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Resource type model
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
}
