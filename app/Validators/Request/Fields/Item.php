<?php
declare(strict_types=1);

namespace App\Validators\Request\Fields;

use App\Validators\Request\Fields\Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for items, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Item extends BaseValidator
{
    /**
     * Return the validator object for the create request
     *
     * @param array $options
     *
     * @return Validator
     */
    public function create(array $options = []): Validator
    {
        $messages = [];
        foreach (Config::get('api.item.validation.POST.messages') as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        };

        return ValidatorFacade::make(
            request()->all(),
            Config::get('api.item.validation.POST.fields'),
            $messages
        );
    }

    /**
     * Return the validator object for the update request
     *
     * @param array $options
     *
     * @return Validator
     */
    public function update(array $options = []): Validator
    {
        $messages = [];
        foreach (Config::get('api.item.validation.PATCH.messages') as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        };

        return ValidatorFacade::make(
            request()->all(),
            Config::get('api.item.validation.PATCH.fields'),
            $messages
        );
    }
}
