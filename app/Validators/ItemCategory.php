<?php

namespace App\Validators;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for item category, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategory
{
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
            Config::get('routes.item_category.validation.POST.fields'),
            Config::get('routes.item_category.validation.POST.messages')
        );
    }

    /**
     * Return the validator object for the update request
     *
     * @param Request $request
     *
     * @return Validator
     */
    static public function update(Request $request): Validator
    {
        return ValidatorFacade::make(
            $request->all(),
            Config::get('routes.item_category.validation.PATCH.fields'),
            Config::get('routes.item_category.validation.POST.messages')
        );
    }
}
