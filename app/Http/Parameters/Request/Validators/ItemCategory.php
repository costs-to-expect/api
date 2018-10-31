<?php

namespace App\Http\Parameters\Request\Validators;

use Validator as BaseValidator;
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
class ItemCategory extends BaseValidator
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
        $decode = $this->hash->category()->decode($request->input('category_id'));
        $category_id = null;
        if (count($decode) === 1) {
            $category_id = $decode[0];
        }

        return ValidatorFacade::make(
            ['category_id' => $category_id],
            Config::get('api.routes.item_category.validation.POST.fields'),
            Config::get('api.routes.item_category.validation.POST.messages')
        );
    }
}
