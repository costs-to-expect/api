<?php

namespace App\Transformers;

use App\Utilities\Hash;

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

    public function __construct()
    {
        $this->hash = new Hash();
    }

    abstract public function toArray(): array;

    public function arrayToJson(array $array = []): string
    {
        return json_encode($array);
    }

    public function jsonToArray(string $json = ''): array
    {
        return json_decode($json, true);
    }
}
