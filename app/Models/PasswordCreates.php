<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class PasswordCreates extends Model
{
    protected $table = 'password_creates';

    public $timestamps = false;

    public $fillable = [
        'email',
        'token'
    ];

    protected $primaryKey = 'email';
}
