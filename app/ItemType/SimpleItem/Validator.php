<?php
declare(strict_types=1);

namespace App\ItemType\SimpleItem;

use App\ItemType\Entity;
use App\Request\Validate\Validator as BaseValidator;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Validator extends BaseValidator
{
    public function __construct()
    {
        $this->entity = Entity::byType('simple-item');

        parent::__construct();
    }

    /**
     * Return the validator object for the create request
     *
     * @param array $options
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function create(array $options = []): \Illuminate\Contracts\Validation\Validator
    {
        return $this->createItemValidator();
    }

    /**
     * Return a valid validator object for a update (PATCH) request
     *
     * @param array $options
     *
     * @return \Illuminate\Contracts\Validation\Validator|null
     */
    public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator
    {
        return $this->updateItemValidator();
    }
}
