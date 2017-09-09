<?php

namespace Vault\Auth;

use Vault\VaultClient;

class AppRole implements Auth
{

    private $roleId;
    private $secretId;

    private $token = null;
    /**
     * @var VaultClient
     */
    private $vaultClient;

    public function __construct(VaultClient $vaultClient)
    {
        $this->vaultClient = $vaultClient;
    }

    public function with($roleId, $secretId)
    {
        $this->roleId = $roleId;
        $this->secretId = $secretId;

        return $this;
    }

    public function getToken()
    {
        if (is_null($this->token)) {
            $this->token = ''; // update from null to empty string to prevent recursion
            $this->login();
        }

        return $this->token;
    }

    private function login()
    {
        $data = $this->vaultClient->write("auth/approle/login", [
            "role_id"   => $this->roleId,
            "secret_id" => $this->secretId,
        ]);

        $this->token = $data['auth']['client_token'];
    }
}
