<?php
declare(strict_types=1);

namespace App\Rule;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class PermissibleUser implements Rule
{
    private int $resource_type_id;

    public function __construct(int $resource_type_id)
    {
        $this->resource_type_id = $resource_type_id;
    }

    public function passes($attribute, $value): bool
    {
        $exists = DB::table('users')
            ->where('email', '=', $value)
            ->count();

        $not_assigned = DB::table('permitted_user')
            ->join('users', 'permitted_user.user_id', '=', 'users.id')
            ->where('permitted_user.resource_type_id', '=', $this->resource_type_id)
            ->where('users.email', '=', $value)
            ->count();

        return $exists !== 0 && $not_assigned === 0;
    }

    public function message(): string
    {
        return trans(Config::get('api.permitted-user.validation-post.messages')['email.permissible']);
    }
}
