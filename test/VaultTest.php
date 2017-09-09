<?php

namespace Tests;


use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Vault\Auth\Tokens;
use Vault\DefaultVaultConfig;
use Vault\VaultClient;

class VaultTest extends TestCase
{

    private $pid;

    public function setup()
    {

        $cmd = "vault server -dev -dev-root-token-id=horde";
        exec(sprintf("%s >/dev/null 2>&1 & echo $!", $cmd), $out);

        $this->pid = $out[0];
        usleep(100000);
    }

    public function teardown()
    {
        posix_kill(intval($this->pid), 9);
    }

    public function testDefaultConfig()
    {
        $cfg = new DefaultVaultConfig('http://localhost:8200', new Tokens('horde'), '');

        $this->assertEquals($cfg->getAddress(), "http://localhost:8200");
        $this->assertInstanceOf(Tokens::class, $cfg->getAuthentication());
        $this->assertEquals($cfg->getAuthentication()->getToken(), "horde");
    }

    public function testSecrets()
    {
        // given
        $secrets = [
            "foo" => "bar",
            "baz" => "boo",
        ];


        // when
        $config = new DefaultVaultConfig('http://localhost:8200', new Tokens('horde'), '');
        $vaultClient = new VaultClient(new Client());
        $vaultClient->withConfig($config);
        $vaultClient->write('secret/testing', $secrets);
        $found = $vaultClient->read('secret/testing');
                // then
        $this->assertEquals($secrets, $found['data'], "read secrets should match written secrets");
    }
}
