<?php
declare(strict_types=1);

namespace App\Validators\Request\Fields;

use App\Validators\Request\Fields\Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for resource types, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType extends BaseValidator
{
    /**
     * Return the validator object for the create request
     *
     * @param Request $request
     *
     * @return Validator
     */
    public function create(Request $request): Validator
    {
        $messages = [];
        foreach (Config::get('api.resource-type.validation.POST.messages') as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        };

        return ValidatorFacade::make(
            $request->all(),
            Config::get('api.resource-type.validation.POST.fields'),
            $this->translateMessages('api.resource-type.validation.POST.messages')
        );
    }
}
