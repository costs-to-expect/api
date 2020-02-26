<?php
declare(strict_types=1);

namespace App\Validators\Fields;

use App\Validators\Fields\Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for categories, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends BaseValidator
{
    /**
     * Create the validation rules for the create request
     *
     * @param integer $resource_type_id
     *
     * @return array
     */
    private function createRules(int $resource_type_id = null): array
    {
        return array_merge(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:category,name,null,id,resource_type_id,' . $resource_type_id
                ],
            ],
            Config::get('api.category.validation.POST.fields')
        );
    }

    /**
     * Create the validation rules for the update request
     *
     * @param integer $category_id
     * @param integer $resource_type_id
     *
     * @return array
     */
    private function updateRules(int $category_id, int $resource_type_id): array
    {
        return array_merge(
            [
                'name' => [
                    'sometimes',
                    'string',
                    'max:255',
                    'unique:category,name,'. $category_id . ',id,resource_type_id,' . $resource_type_id
                ],
            ],
            Config::get('api.category.validation.PATCH.fields')
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
        $this->requiredIndexes(['resource_type_id'], $options);

        return ValidatorFacade::make(
            request()->all(),
            $this->createRules(intval($options['resource_type_id'])),
            $this->translateMessages('api.category.validation.POST.messages')
        );
    }

    /**
     * @param array $options
     *
     * @return Validator
     */
    public function update(array $options = []): Validator
    {
        return ValidatorFacade::make(
            request()->all(),
            $this->updateRules($options['resource_type_id'], $options['category_id']),
            $this->translateMessages('api.category.validation.PATCH.messages')
        );
    }
}