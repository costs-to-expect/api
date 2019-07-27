<?php
declare(strict_types=1);

namespace App\Validators\Request\Fields;

use App\Validators\Request\Fields\Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for item category, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategory extends BaseValidator
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
        $decode = $this->hash->category()->decode(request()->input('category_id'));
        $category_id = null;
        if (count($decode) === 1) {
            $category_id = $decode[0];
        }

        return ValidatorFacade::make(
            ['category_id' => $category_id],
            Config::get('api.item-category.validation.POST.fields'),
            $this->translateMessages('api.item-category.validation.POST.messages')
        );
    }

    /**
     * @param array $options
     *
     * @return Validator
     */
    public function update(array $options = []): Validator
    {
        // TODO: Implement update() method.
    }
}
