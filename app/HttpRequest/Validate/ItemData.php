<?php

declare(strict_types=1);

namespace App\HttpRequest\Validate;

use App\HttpRequest\Validate\Validator as BaseValidator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemData extends BaseValidator
{
    private function createRules(int $item_id): array
    {
        return [
            ...[
                'key' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('item_data', 'key')->where('item_id', $item_id)
                ]
            ],
            ...Config::get('api.item-data.validation-post.fields')
        ];
    }

    public function create(array $options = []): \Illuminate\Contracts\Validation\Validator
    {
        $this->requiredIndexes(['item_id'], $options);

        return ValidatorFacade::make(
            request()->only('key', 'value'),
            $this->createRules((int) $options['item_id']),
            $this->translateMessages('api.item-data.validation-post.messages')
        );
    }

    public function update(array $options = []): \Illuminate\Contracts\Validation\Validator
    {
        return ValidatorFacade::make(
            request()->only(['value']),
            Config::get('api.item-data.validation-patch.fields')
        );
    }
}
