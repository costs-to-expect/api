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
    protected $hash_category;
    protected $hash_sub_category;
    protected $hash_resource_type;
    protected $hash_resource;
    protected $hash_item;
    protected $hash_item_category;
    protected $hash_item_sub_category;

    public function __construct()
    {
        $min_length = Config::get('api.hashids.min_length');

        $this->hash_category = new Hashids(Config::get('api.hashids.category'), $min_length);
        $this->hash_sub_category = new Hashids(Config::get('api.hashids.sub_category'), $min_length);
        $this->hash_resource_type = new Hashids(Config::get('api.hashids.resource_type'), $min_length);
        $this->hash_resource = new Hashids(Config::get('api.hashids.resource'), $min_length);
        $this->hash_item = new Hashids(Config::get('api.hashids.item'), $min_length);
        $this->hash_item_category = new Hashids(Config::get('api.hashids.item_category'), $min_length);
        $this->hash_item_sub_category = new Hashids(Config::get('api.hashids.item_sub_category'), $min_length);
    }

    abstract public function toArray(): array;
}
