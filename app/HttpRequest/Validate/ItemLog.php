<?php

declare(strict_types=1);

namespace App\HttpRequest\Validate;

use App\HttpRequest\Validate\Validator as BaseValidator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemLog extends BaseValidator
{
    public function create(array $options = []): \Illuminate\Contracts\Validation\Validator
    {
        return ValidatorFacade::make(
            request()->only('message', 'parameters'),
            Config::get('api.item-log.validation-post.fields')
        );
    }

    public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator
    {
        return null;
    }
}
