<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ResourceTypeName implements Rule
{
    private $user_id;
    private $resource_type_id;

    /**
     * Create a new rule instance.
     *
     * @param integer $user_id
     * @param integer|null $resource_type_id
     *
     * @return void
     */
    public function __construct(int $user_id, ?int $resource_type_id = null)
    {
        $this->user_id = $user_id;
        $this->resource_type_id = $resource_type_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $where_clauses = [
            ['permitted_user.user_id', '=', $this->user_id],
            ['resource_type.name', '=', $value]
        ];

        if ($this->resource_type_id !== null) {
            $where_clauses[] = [
                'resource_type.id', '!=', $this->resource_type_id
            ];
        }

        $count = DB::table('resource_type')->
            join('permitted_users', 'resource_type.id', '=', 'permitted_user.resource_type_id')->
            where($where_clauses)->
            count();

        return $count > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans(Config::get('api.resource-type.validation.POST.messages')['name.unique']);
    }
}
