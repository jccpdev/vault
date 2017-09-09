<?php

namespace Vault;

use Vault\Auth\Auth;

interface VaultConfig
{

    /**
     * @return mixed
     */
    public function getAddress();

    /**
     * @return Auth
     */
    public function getAuthentication();

    /**
     * @return mixed
     */
    public function getHostName();

}
