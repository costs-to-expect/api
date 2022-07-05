<?php

declare(strict_types=1);

namespace App\HttpRequest\Validate;

use App\HttpRequest\Validate\Validator as BaseValidator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for subcategories, returns the generated
 * validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
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
            Config::get('api.subcategory.validation-post.fields')
        );
    }

    /**
     * Create the validation rules for the update (PATCH) request
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
                    'unique:sub_category,name,' . $subcategory_id . ',id,category_id,' . $category_id
                ],
            ],
            Config::get('api.subcategory.validation-patch.fields')
        );
    }

    /**
     * Any fields which can't be defined via the configuration files because
     * the validation rules are dynamic
     *
     * @return array|string[]
     */
    public function dynamicDefinedFields(): array
    {
        return ['name'];
    }

    /**
     * Return a valid validator object for a create (POST) request
     *
     * @param array $options
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function create(array $options = []): \Illuminate\Contracts\Validation\Validator
    {
        $this->requiredIndexes(['category_id'], $options);

        return ValidatorFacade::make(
            request()->all(),
            $this->createRules((int) $options['category_id']),
            $this->translateMessages('api.subcategory.validation-post.messages')
        );
    }

    public function update(array $options = []): \Illuminate\Contracts\Validation\Validator
    {
        return ValidatorFacade::make(
            request()->all(),
            $this->updateRules((int) $options['category_id'], (int) $options['subcategory_id']),
            $this->translateMessages('api.subcategory.validation-patch.messages')
        );
    }
}
