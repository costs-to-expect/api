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
        $min_length = Config::get('api.hashids.min_length');

        $this->hash = new Hashids('costs-to-expect', Config::get($min_length));
        $this->hash_category = new Hashids(Config::get('api.hashids.category'), Config::get($min_length));
    }

    abstract public function toArray(): array;
}
