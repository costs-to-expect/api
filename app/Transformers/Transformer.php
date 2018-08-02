<?php

namespace App\Transformers;

use Hashids\Hashids;
use Illuminate\Support\Facades\Config;

/**
 * Base transformer class, sets up the interface and includes helper methods
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
abstract class Transformer
{
    protected $hash;
    protected $hash_category;

    public function __construct()
    {
        $this->hash = new Hashids('costs-to-expect', 10);
        $this->hash_category = new Hashids(Config::get('api.hashids.category'), 10);
    }

    abstract public function toArray(): array;
}
