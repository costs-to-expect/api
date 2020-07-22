<?php
declare(strict_types=1);

namespace App\Option\Value;

use App\Request\Hash;

class Value
{
    protected Hash $hash;

    public function __construct()
    {
        $this->hash = new Hash();
    }
}
