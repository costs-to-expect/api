<?php
declare(strict_types=1);

namespace App\Utilities;

use Hashids\Hashids;
use Illuminate\Support\Facades\Config;

/**
 * Utility hash class to encode and decode strings by type
 *
 * As with all utility classes, eventually they may be moved into libraries if
 * they gain more than a few functions and the creation of a library makes
 * sense.
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Hash
{
    private array $hashers;

    public function __construct()
    {
        $this->hashers = [];

        $this->setUp();

        return $this;
    }

    private function setUp()
    {
        $config = Config::get('api.app.hashids');

        $min_length = $config['min_length'];

        $this->hashers['category'] = new Hashids($config['category'], $min_length);
        $this->hashers['subcategory'] = new Hashids($config['subcategory'], $min_length);
        $this->hashers['resource_type'] = new Hashids($config['resource_type'], $min_length);
        $this->hashers['resource'] = new Hashids($config['resource'], $min_length);
        $this->hashers['item'] = new Hashids($config['item'], $min_length);
        $this->hashers['item_category'] = new Hashids($config['item_category'], $min_length);
        $this->hashers['item_partial_transfer'] = new Hashids($config['item_partial_transfer'], $min_length);
        $this->hashers['item_subcategory'] = new Hashids($config['item_subcategory'], $min_length);
        $this->hashers['item_transfer'] = new Hashids($config['item_transfer'], $min_length);
        $this->hashers['item_type'] = new Hashids($config['item_type'], $min_length);
        $this->hashers['permitted_user'] = new Hashids($config['permitted_user'], $min_length);
        $this->hashers['user'] = new Hashids($config['user'], $min_length);
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
        }

        return false;
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
                return (int) $id[0];
            }

            return false;
        }

        return false;
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
        return $this->hashers['subcategory'];
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
     * Helper method to return the item partial transfer hash object
     *
     * @return Hashids
     */
    public function itemPartialTransfer(): Hashids
    {
        return $this->hashers['item_partial_transfer'];
    }

    /**
     * Helper method to return the item sub category hash object
     *
     * @return Hashids
     */
    public function itemSubCategory(): Hashids
    {
        return $this->hashers['item_subcategory'];
    }

    /**
     * Helper method to return the item transfer hash object
     *
     * @return Hashids
     */
    public function itemTransfer(): Hashids
    {
        return $this->hashers['item_transfer'];
    }

    /**
     * Helper method to return the item type object
     *
     * @return Hashids
     */
    public function itemType(): Hashids
    {
        return $this->hashers['item_type'];
    }

    /**
     * Helper method to return the permitted user object
     *
     * @return Hashids
     */
    public function permittedUser(): Hashids
    {
        return $this->hashers['permitted_user'];
    }

    /**
     * Helper method to return the user object
     *
     * @return Hashids
     */
    public function user(): Hashids
    {
        return $this->hashers['user'];
    }
}
