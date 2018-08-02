<?php

namespace App\Validators;

use App\Validators\Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for resource types, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType extends BaseValidator
{
    /**
     * Create the validation rules for the update (PATCH) request
     *
     * @param integer $resource_type_id
     *
     * @return array
     */
    private function updateRules(int $resource_type_id): array
    {
        return array_merge(
            [
                'name' => [
                    'required',
                    'string',
                    'unique:resource_type,name,' . $resource_type_id . ',id'
                ],
            ],
            Config::get('api.routes.resource_type.validation.PATCH.fields')
        );
    }

    /**
     * Return the validator object for the create request
     *
     * @param Request $request
     *
     * @return Validator
     */
    public function create(Request $request): Validator
    {
        return ValidatorFacade::make(
            $request->all(),
            Config::get('api.routes.resource_type.validation.POST.fields'),
            Config::get('api.routes.resource_type.validation.POST.messages')
        );
    }

    /**
     * Return the validator object for the update request
     *
     * @param Request $request
     * @param integer $resource_type_id
     *
     * @return Validator
     */
    public function update(Request $request, int $resource_type_id): Validator
    {
        return ValidatorFacade::make(
            $request->all(),
            self::updateRules($resource_type_id),
            Config::get('api.routes.resource_type.validation.POST.messages')
        );
    }
}
