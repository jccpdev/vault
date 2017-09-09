<?php

namespace Tests\Unit;



use PHPUnit\Framework\TestCase;
use Vault\Auth\Tokens;
use Vault\DefaultVaultConfig;
use Vault\VaultClient;
use Mockery as m;


class DefaultVaultConfigTest extends TestCase
{

    public function test_config_can_be_build_with_params()
    {
        $address = 'theAddress';
        $auth = new Tokens('theToken');
        $hostName = 'theHostName';

        $config = new DefaultVaultConfig($address, $auth, $hostName);

        $this->assertEquals($address, $config->getAddress());
        $this->assertEquals($auth, $config->getAuthentication());
        $this->assertEquals($hostName, $config->getHostName());

    }

}