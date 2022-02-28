<?php
declare(strict_types=1);

namespace App\Request\Validate;

use App\Request\Validate\Validator as BaseValidator;
use App\Rules\PermissibleUser;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
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
            $this->translateMessages('api.resource.validation.POST.messages')
        );
    }

    public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator
    {
        return null;
    }
}