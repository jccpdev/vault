<?php

namespace Vault;

use Vault\Auth\Auth;

class DefaultVaultConfig implements VaultConfig
{

    private $address;
    private $authentication;
    private $hostName;

    public function __construct($address, Auth $auth, $hostName = null)
    {
        $this->address = $address;
        $this->authentication = $auth;
        $this->hostName = $hostName;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return mixed
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @return mixed
     */
    public function getHostName()
    {
        return $this->hostName;
    }

}
