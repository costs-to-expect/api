<?php

namespace App\Validators;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for items, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Item
{
    /**
     * Return the validator object for the create request
     *
     * @param Request $request
     * @param integer $resource_type_id
     * @param integer $resource_id
     *
     * @return Validator
     */
    static public function create(Request $request, int $resource_type_id, int $resource_id): Validator
    {
        return ValidatorFacade::make(
            $request->all(),
            Config::get('routes.item.validation.POST.fields'),
            Config::get('routes.item.validation.POST.messages')
        );
    }

    /**
     * Return the validator object for the update request
     *
     * @param Request $request
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer $item_id
     *
     * @return Validator
     */
    static public function update(Request $request, int $resource_type_id, int $resource_id, int $item_id): Validator
    {
        return ValidatorFacade::make(
            $request->all(),
            Config::get('routes.item.validation.PATCH.fields'),
            Config::get('routes.item.validation.POST.messages')
        );
    }
}
