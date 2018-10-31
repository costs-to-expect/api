<?php

namespace App\Http\Parameters\Request\Validators;

use App\Utilities\Hash;

/**
 * Base validator class, sets up the interface and includes helper methods
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
abstract class Validator
{
    protected $hash;

    public function __construct()
    {
        $this->hash = new Hash();
    }
}
