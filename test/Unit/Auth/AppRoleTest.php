<?php

use PHPUnit\Framework\TestCase;
use Vault\Auth\AppRole;
use Mockery as m;
use Vault\VaultClient;

class AppRoleTest extends TestCase
{
    /** @var  m\MockInterface */
    public $vaultClientMock;

    /** @var  AppRole */
    public $appRole;

    /** @before */
    public function setUpAppRole()
    {
        $this->vaultClientMock = m::mock(VaultClient::class);
        $this->appRole = new AppRole($this->vaultClientMock);
    }

    public function test_it_can_get_a_token()
    {
        $this->vaultClientMock->shouldReceive('write')
            ->with('auth/approle/login', [
                'role_id' => 'theRoleId', 'secret_id' => 'theSecretId',
            ])
            ->andReturn(['auth' => ['client_token' => 'theClientToken']])
            ->once();

        $token = $this->appRole
            ->with('theRoleId', 'theSecretId')
            ->getToken();

        $this->assertEquals('theClientToken', $token);
    }

    public function test_it_can_get_a_token_from_cache()
    {
        $this->vaultClientMock->shouldReceive('write')
            ->with('auth/approle/login', [
                'role_id' => 'theRoleId', 'secret_id' => 'theSecretId',
            ])
            ->andReturn(['auth' => ['client_token' => 'theClientToken']])
            ->once();

        $this->appRole->with('theRoleId', 'theSecretId');

        $this->appRole->getToken();
        $token = $this->appRole->getToken();


        $this->assertEquals('theClientToken', $token);
    }

}