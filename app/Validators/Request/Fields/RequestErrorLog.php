<?php
declare(strict_types=1);

namespace App\Validators\Request\Fields;

use App\Validators\Request\Fields\Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for request errors, returns the generated validator
 * object
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestErrorLog extends BaseValidator
{
    /**
     * Return the validator object for the create request
     *
     * @param array $options
     *
     * @return Validator
     */
    public function create(array $options = []): Validator
    {
        return ValidatorFacade::make(
            request()->all(),
            Config::get('api.request-error-log.validation.POST.fields'),
            $this->translateMessages('api.request-error-log.validation.POST.messages')
        );
    }

    public function update(array $options = []): Validator
    {
        // TODO: Implement update() method.
    }
}
