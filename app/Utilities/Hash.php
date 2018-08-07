<?php

namespace App\Utilities;

use Hashids\Hashids;
use Illuminate\Support\Facades\Config;

class Hash
{
    private $min_length;
    private $hashers;

    public function __construct()
    {
        $this->min_length = Config::get('api.hashids.min_length');

        $this->hashers = [];

        $this->setUp();

        return $this;
    }

    private function setUp()
    {
        $this->hashers['category'] = new Hashids(Config::get('api.hashids.category'), $this->min_length);
        $this->hashers['sub_category'] = new Hashids(Config::get('api.hashids.sub_category'), $this->min_length);
        $this->hashers['resource_type'] = new Hashids(Config::get('api.hashids.resource_type'), $this->min_length);
        $this->hashers['resource'] = new Hashids(Config::get('api.hashids.resource'), $this->min_length);
        $this->hashers['item'] = new Hashids(Config::get('api.hashids.item'), $this->min_length);
        $this->hashers['item_category'] = new Hashids(Config::get('api.hashids.item_category'), $this->min_length);
        $this->hashers['item_sub_category'] = new Hashids(Config::get('api.hashids.item_sub_category'), $this->min_length);
    }

    /**
     * @param string $type
     * @param int $parameter
     *
     * @return false|string
     */
    public function encode(string $type, int $parameter)
    {
        if (array_key_exists($type, $this->hashers) === true) {
            return $this->hashers[$type]->encode($parameter);
        } else {
            return false;
        }
    }

    /**
     * @param string $type
     * @param string $parameter
     *
     * @return false|integer
     */
    public function decode(string $type, string $parameter)
    {
        if (array_key_exists($type, $this->hashers) === true) {
            $id = $this->hashers[$type]->decode($parameter);
            if (is_array($id) && array_key_exists(0, $id)) {
                return intval($id[0]);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
