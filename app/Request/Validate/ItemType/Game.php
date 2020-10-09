<?php
declare(strict_types=1);

namespace App\Request\Validate\ItemType;

use App\Entity\Item\Entity;
use App\Request\Validate\Validator as BaseValidator;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Game extends BaseValidator
{
    public function __construct()
    {
        $this->entity = Entity::byType('game');

        parent::__construct();
    }

    public function create(array $options = []): \Illuminate\Contracts\Validation\Validator
    {
        return $this->createItemValidator();
    }

    public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator
    {
        return $this->updateItemValidator();
    }
}
