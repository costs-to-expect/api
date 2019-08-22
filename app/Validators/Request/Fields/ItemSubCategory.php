<?php
declare(strict_types=1);

namespace App\Validators\Request\Fields;

use App\Validators\Request\Fields\Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;

/**
 * Validation helper class for item sub category, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
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
            Config::get('api.item-subcategory.validation.POST.fields')
        );
    }

    /**
     * Return the validator object for the create request
     *
     * @param array $options
     *
     * @return Validator
     */
    public function create(array $options = []): Validator
    {
        $this->requiredIndexes(['category_id'], $options);

        $decode = $this->hash->subCategory()->decode(request()->input('sub_category_id'));
        $sub_category_id = null;
        if (count($decode) === 1) {
            $sub_category_id = $decode[0];
        }

        return ValidatorFacade::make(
            ['sub_category_id' => $sub_category_id],
            self::createRules(intval($options['category_id'])),
            $this->translateMessages('api.item-subcategory.validation.POST.messages')
        );
    }

    /**
     * @param array $options
     * @return Validator
     */
    public function update(array $options = []): Validator
    {
        // TODO: Implement update() method.
    }
}
