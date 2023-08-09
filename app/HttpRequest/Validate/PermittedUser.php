<?php

declare(strict_types=1);

namespace App\HttpRequest\Validate;

use App\HttpRequest\Validate\Validator as BaseValidator;
use App\Rule\PermissibleUser;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class PermittedUser extends BaseValidator
{
    public function create(array $options = []): \Illuminate\Contracts\Validation\Validator
    {
        $this->requiredIndexes(['resource_type_id'], $options);

        return ValidatorFacade::make(
            request()->only(['email']),
            [
                'email' => [
                    'required',
                    'email',
                    new PermissibleUser($options['resource_type_id'])
                ],

            ],
            $this->translateMessages('api.resource.validation-post.messages')
        );
    }

    public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator
    {
        return null;
    }
}
