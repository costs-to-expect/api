<?php

declare(strict_types=1);

namespace App\HttpRequest\Validate;

use App\HttpRequest\Validate\Validator as BaseValidator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for categories, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends BaseValidator
{
    private function createRules(int $resource_type_id): array
    {
        return [
            ...[
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:category,name,null,id,resource_type_id,' . $resource_type_id
                ],
            ],
            ...Config::get('api.category.validation-post.fields')
        ];
    }

    private function updateRules(int $category_id, int $resource_type_id): array
    {
        return [
            ...[
                'name' => [
                    'sometimes',
                    'string',
                    'max:255',
                    'unique:category,name,' . $category_id . ',id,resource_type_id,' . $resource_type_id
                ],
            ],
            ...Config::get('api.category.validation-patch.fields')
        ];
    }

    public function dynamicDefinedFields(): array
    {
        return ['name'];
    }

    public function create(array $options = []): \Illuminate\Contracts\Validation\Validator
    {
        $this->requiredIndexes(['resource_type_id','item_type'], $options);

        return ValidatorFacade::make(
            request()->only(['name', 'description']),
            $this->createRules((int) $options['resource_type_id']),
            $this->translateMessages(
                ($options['item_type'] === 'game') ?
                    'api.category.validation-post.messages-game' :
                    'api.category.validation-post.messages'
            )
        );
    }

    public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator
    {
        return ValidatorFacade::make(
            request()->only(['name', 'description']),
            $this->updateRules($options['category_id'], $options['resource_type_id']),
            $this->translateMessages('api.category.validation-patch.messages')
        );
    }
}
