<?php
declare(strict_types=1);

namespace App\Request;

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
    /**
     * @var Hashids[]
     */
    private array $hashers;

    public function __construct()
    {
        $this->hashers = [];

        $this->setUp();
    }

    private function setUp(): void
    {
        $config = Config::get('api.app.hashids');

        $min_length = $config['min-length'];

        $this->hashers['category'] = new Hashids($config['category'], $min_length);
        $this->hashers['subcategory'] = new Hashids($config['subcategory'], $min_length);
        $this->hashers['resource-type'] = new Hashids($config['resource-type'], $min_length);
        $this->hashers['resource'] = new Hashids($config['resource'], $min_length);
        $this->hashers['item'] = new Hashids($config['item'], $min_length);
        $this->hashers['item-category'] = new Hashids($config['item-category'], $min_length);
        $this->hashers['item-partial-transfer'] = new Hashids($config['item-partial-transfer'], $min_length);
        $this->hashers['item-subcategory'] = new Hashids($config['item-subcategory'], $min_length);
        $this->hashers['item-transfer'] = new Hashids($config['item-transfer'], $min_length);
        $this->hashers['item-type'] = new Hashids($config['item-type'], $min_length);
        $this->hashers['permitted-user'] = new Hashids($config['permitted-user'], $min_length);
        $this->hashers['user'] = new Hashids($config['user'], $min_length);
        $this->hashers['currency'] = new Hashids($config['currency'], $min_length);
        $this->hashers['queue'] = new Hashids($config['queue'], $min_length);
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

    public function category(): Hashids
    {
        return $this->hashers['category'];
    }

    public function subcategory(): Hashids
    {
        return $this->hashers['subcategory'];
    }

    public function resourceType(): Hashids
    {
        return $this->hashers['resource-type'];
    }

    public function resource(): Hashids
    {
        return $this->hashers['resource'];
    }

    public function item(): Hashids
    {
        return $this->hashers['item'];
    }

    public function itemCategory(): Hashids
    {
        return $this->hashers['item-category'];
    }

    public function itemPartialTransfer(): Hashids
    {
        return $this->hashers['item-partial-transfer'];
    }

    public function itemSubcategory(): Hashids
    {
        return $this->hashers['item-subcategory'];
    }

    public function itemTransfer(): Hashids
    {
        return $this->hashers['item-transfer'];
    }

    public function itemType(): Hashids
    {
        return $this->hashers['item-type'];
    }

    public function permittedUser(): Hashids
    {
        return $this->hashers['permitted-user'];
    }

    public function user(): Hashids
    {
        return $this->hashers['user'];
    }

    public function currency(): Hashids
    {
        return $this->hashers['currency'];
    }

    public function queue(): Hashids
    {
        return $this->hashers['queue'];
    }
}
