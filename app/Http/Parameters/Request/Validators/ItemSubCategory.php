<?php

namespace App\Http\Parameters\Request\Validators;

use Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;

/**
 * Validation helper class for item sub category, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubCategory extends BaseValidator
{
    private $category_id;

    /**
     * Create the validation rules for the create (POST) request
     *
     * @param integer $category_id
     *
     * @return array
     */
    private function createRules(int $category_id): array
    {
        $this->category_id = $category_id;

        return array_merge(
            [
                'sub_category_id' => [
                    'required',
                    Rule::exists('sub_category', 'id')->where(function ($query)
                    {
                        $query->where('category_id', '=', $this->category_id);
                    }),

                ],
            ],
            Config::get('api.routes.item_sub_category.validation.POST.fields')
        );
    }

    /**
     * Return the validator object for the create request
     *
     * @param Request $request
     * @param integer $category_id
     *
     * @return Validator
     */
    public function create(Request $request, $category_id): Validator
    {
        $decode = $this->hash->subCategory()->decode($request->input('sub_category_id'));
        $sub_category_id = null;
        if (count($decode) === 1) {
            $sub_category_id = $decode[0];
        }

        return ValidatorFacade::make(
            ['sub_category_id' => $sub_category_id],
            self::createRules($category_id),
            Config::get('api.routes.item_sub_category.validation.POST.messages')
        );
    }
}
