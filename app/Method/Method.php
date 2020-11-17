<?php
declare(strict_types=1);

namespace App\Method;

use App\Entity\Api;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
abstract class Method
{
    protected bool $authentication;

    protected bool $authenticated;

    protected $description;

    protected Api $api_config;

    public function __construct()
    {
        $this->authentication = false;
        $this->authenticated = false;
        $this->description = '';

        $this->api_config = new Api();
    }

    public function setAuthenticationRequirement(
        bool $status = false
    ): Method
    {
        $this->authentication = $status;

        return $this;
    }

    public function setAuthenticationStatus(
        bool $status = false
    ): Method
    {
        $this->authenticated = $status;

        return $this;
    }

    public function setDescription(
        string $localisation_path
    ): Method
    {
        $this->description = trans($localisation_path);

        return $this;
    }

    abstract protected function mergeAndLocalise();

    abstract public function option(): array;
}
