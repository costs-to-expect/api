<?php
declare(strict_types=1);

namespace App\Request\Validate;

use App\Request\Validate\Validator as BaseValidator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for request errors, returns the generated validator
 * object
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestErrorLog extends BaseValidator
{
    /**
     * Return a valid validator object for a create (POST) request
     *
     * @param array $options
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function create(array $options = []): \Illuminate\Contracts\Validation\Validator
    {
        return ValidatorFacade::make(
            request()->all(),
            Config::get('api.request-error-log.validation.POST.fields'),
            $this->translateMessages('api.request-error-log.validation.POST.messages')
        );
    }

    public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator
    {
        return null;
    }
}
