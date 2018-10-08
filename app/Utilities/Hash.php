<?php

namespace App\Utilities;

use Hashids\Hashids;
use Illuminate\Support\Facades\Config;

/**
 * Utility hash class to encode and decode strings by type
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Hash
{
    private $min_length;
    private $hashers;

    public function __construct()
    {
        $this->hashers = [];

        $this->setUp();

        return $this;
    }

    private function setUp()
    {
        $this->min_length = Config::get('api.hashids.min_length');

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

    /**
     * Helper method to return the category hash object
     *
     * @return Hashids
     */
    public function category(): Hashids
    {
        return $this->hashers['category'];
    }

    /**
     * Helper method to return the sub category hash object
     *
     * @return Hashids
     */
    public function subCategory(): Hashids
    {
        return $this->hashers['sub_category'];
    }

    /**
     * Helper method to return the resource type hash object
     *
     * @return Hashids
     */
    public function resourceType(): Hashids
    {
        return $this->hashers['resource_type'];
    }

    /**
     * Helper method to return the resource hash object
     *
     * @return Hashids
     */
    public function resource(): Hashids
    {
        return $this->hashers['resource'];
    }

    /**
     * Helper method to return the item hash object
     *
     * @return Hashids
     */
    public function item(): Hashids
    {
        return $this->hashers['item'];
    }

    /**
     * Helper method to return the item category hash object
     *
     * @return Hashids
     */
    public function itemCategory(): Hashids
    {
        return $this->hashers['item_category'];
    }

    /**
     * Helper method to return the item sub category hash object
     *
     * @return Hashids
     */
    public function itemSubCategory(): Hashids
    {
        return $this->hashers['item_sub_category'];
    }
}
