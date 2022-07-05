<?php

declare(strict_types=1);

namespace App\HttpVerb;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
abstract class Verb
{
    protected bool $authentication;

    protected bool $authenticated;

    protected string $description;

    public function __construct()
    {
        $this->authentication = false;
        $this->authenticated = false;
        $this->description = '';
    }

    public function setAuthenticationRequirement(
        bool $status = false
    ): Verb {
        if ($status === true) {
            $this->authentication = true;
        } else {
            $this->authentication = false;
        }

        return $this;
    }

    public function setAuthenticationStatus(
        bool $status = false
    ): Verb {
        if ($status === true) {
            $this->authenticated = true;
        } else {
            $this->authenticated = false;
        }

        return $this;
    }

    public function setDescription(
        string $localisation_path
    ): Verb {
        $this->description = trans($localisation_path);

        return $this;
    }

    abstract protected function mergeAndLocalise();

    abstract public function option(): array;
}
