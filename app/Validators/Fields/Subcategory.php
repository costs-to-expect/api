<?php
declare(strict_types=1);

namespace App\Validators\Fields;

use App\Validators\Fields\Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for sub categories, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Subcategory extends BaseValidator
{
    /**
     * Create the validation rules for the create (POST) request
     *
     * @param integer $category_id
     *
     * @return array
     */
    private function createRules(int $category_id): array
    {
        return array_merge(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:sub_category,name,null,id,category_id,' . $category_id
                ],
            ],
            Config::get('api.subcategory.validation.POST.fields')
        );
    }

    /**
     * Create the validation rules for the update request
     *
     * @param integer $category_id
     * @param integer $subcategory_id
     *
     * @return array
     */
    private function updateRules(int $category_id, int $subcategory_id): array
    {
        return array_merge(
            [
                'name' => [
                    'sometimes',
                    'string',
                    'max:255',
                    'unique:sub_category,name,'. $subcategory_id . ',id,category_id,' . $category_id
                ],
            ],
            Config::get('api.subcategory.validation.PATCH.fields')
        );
    }

    public function dynamicDefinedFields(): array
    {
        return ['name'];
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

        return ValidatorFacade::make(
            request()->all(),
            self::createRules(intval($options['category_id'])),
            $this->translateMessages('api.subcategory.validation.POST.messages')
        );
    }

    /**
     * @param array $options
     * @return Validator
     */
    public function update(array $options = []): Validator
    {
        return ValidatorFacade::make(
            request()->all(),
            $this->updateRules($options['category_id'], $options['subcategory_id']),
            $this->translateMessages('api.subcategory.validation.PATCH.messages')
        );
    }
}
