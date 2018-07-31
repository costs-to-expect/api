<?php

namespace App\Validators;

use App\Validators\Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for resources, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Resource extends BaseValidator
{
    /**
     * Create the validation rules for the create request
     *
     * @param integer $resource_type_id
     *
     * @return array
     */
    private function createRules(int $resource_type_id): array
    {
        return array_merge(
            [
                'name' => [
                    'required',
                    'string',
                    'unique:resource,name,null,id,resource_type_id,' . $resource_type_id
                ],
            ],
            Config::get('routes.resource.validation.POST.fields')
        );
    }

    /**
     * Create the validation rules for the update (PATCH) request
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     *
     * @return array
     */
    private function updateRules(int $resource_type_id, int $resource_id): array
    {
        return array_merge(
            [
                'name' => [
                    'required',
                    'string',
                    'unique:resource,name,' . $resource_id . ',id,resource_type_id,' . $resource_type_id
                ],
            ],
            Config::get('routes.resource.validation.PATCH.fields')
        );
    }

    /**
     * Return the validator object for the create request
     *
     * @param Request $request
     * @param integer $resource_type_id
     *
     * @return Validator
     */
    public function create(Request $request, int $resource_type_id): Validator
    {
        return ValidatorFacade::make(
            $request->all(),
            self::createRules($resource_type_id),
            Config::get('routes.resource.validation.POST.messages')
        );
    }

    /**
     * Return the validator object for the update request
     *
     * @param Request $request
     * @param integer $resource_type_id
     * @param integer $resource_id
     *
     * @return Validator
     */
    public function update(Request $request, int $resource_type_id, int $resource_id): Validator
    {
        return ValidatorFacade::make(
            $request->all(),
            self::updateRules($resource_type_id, $resource_id),
            Config::get('routes.resource.validation.POST.messages')
        );
    }
}
