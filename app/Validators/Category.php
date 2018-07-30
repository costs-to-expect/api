<?php

namespace App\Validators;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for categories, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category
{
    /**
     * Create the validation rules for the update (PATCH) request
     *
     * @param integer $category_id
     *
     * @return array
     */
    static private function updateRules(int $category_id): array
    {
        return array_merge(
            [
                'name' => [
                    'required',
                    'string',
                    'unique:category,name,' . $category_id . ',id'
                ],
            ],
            Config::get('routes.category.validation.PATCH.fields')
        );
    }

    /**
     * Return the validator object for the create request
     *
     * @param Request $request
     *
     * @return Validator
     */
    static public function create(Request $request): Validator
    {
        return ValidatorFacade::make(
            $request->all(),
            Config::get('routes.category.validation.POST.fields'),
            Config::get('routes.category.validation.POST.messages')
        );
    }

    /**
     * Return the validator object for the update request
     *
     * @param Request $request
     * @param integer $category_id
     *
     * @return Validator
     */
    static public function update(Request $request, int $category_id): Validator
    {
        return ValidatorFacade::make(
            $request->all(),
            self::updateRules($category_id),
            Config::get('routes.category.validation.POST.messages')
        );
    }
}
