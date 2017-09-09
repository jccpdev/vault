<?php

namespace Tests\Unit;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Vault\Auth\Tokens;
use Vault\DefaultVaultConfig;
use Vault\VaultClient;
use Vault\VaultConfig;

class VaultClientTest extends TestCase
{
    /** @var  m\MockInterface */
    public $clientMock;

    /** @var  VaultClient */
    public $vaultClient;

    /** @var  VaultConfig */
    public $vaultConfig;

    /** @before */
    public function setUpClient()
    {
        $this->clientMock = m::mock(Client::class);
        $this->vaultClient = new VaultClient($this->clientMock);
        $this->vaultConfig = new DefaultVaultConfig('theAddress', new Tokens('theToken'), 'theHostName');
    }

    public function test_it_can_take_vault_config()
    {
        $this->vaultClient->withConfig($this->vaultConfig);
        $this->assertEquals($this->vaultClient->getConfig(), $this->vaultConfig);
    }

    public function test_it_can_make_a_write_request()
    {
        $path = 'thePath';
        $body = ['the' => 'params'];
        $expectedData = ['some' => 'json'];

        $this->vaultClient->withConfig($this->vaultConfig);

        $this->setRequestMocks('POST', $path, $body, $expectedData);

        $data = $this->vaultClient->write($path, $body);

        $this->assertEquals($expectedData, $data);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage error1
     */
    public function test_it_can_throw_exception_with_errors()
    {
        $path = 'thePath';
        $body = ['the' => 'params'];
        $expectedData = ['some' => 'json'];

        $this->vaultClient->withConfig($this->vaultConfig);

        $this->clientMock->shouldReceive('request')
            ->with('POST',
                "{$this->vaultConfig->getAddress()}/v1/{$path}",
                [
                    'headers' => [
                        'X-VAULT-TOKEN' => $this->vaultConfig->getAuthentication()->getToken($this->vaultClient),
                        'Host'          => $this->vaultConfig->getHostName(),
                    ],
                    'json'    => $body,
                ]
            )
            ->andReturnSelf()
            ->once();

        $theRequest = new Request('POST', '/test');
        $theResponse = new Response(500, [], json_encode(['errors' => ['error1']]));

        $requestException = new RequestException('Something Web Wrong', $theRequest, $theResponse);

        $this->clientMock->shouldReceive('getBody')
            ->andThrow($requestException)
            ->once();

        $data = $this->vaultClient->write($path, $body);

        $this->assertEquals($expectedData, $data);
    }

    public function test_it_can_make_authEnable_request()
    {
        $path = 'sys/auth/someType';
        $body = ["type" => 'someType'];
        $expectedData = ['some' => 'json'];

        $this->vaultClient->withConfig($this->vaultConfig);

        $this->setRequestMocks('POST', $path, $body, $expectedData);

        $data = $this->vaultClient->authEnable('someType');

        $this->assertEquals($expectedData, $data);
    }

    public function test_it_can_make_authDisable_request()
    {
        $path = 'sys/auth/someType';
        $body = ["type" => 'someType'];
        $expectedData = ['some' => 'json'];

        $this->vaultClient->withConfig($this->vaultConfig);

        $this->setRequestMocks('DELETE', $path, $body, $expectedData);

        $data = $this->vaultClient->authDisable('someType');

        $this->assertEquals($expectedData, $data);
    }

    public function test_it_can_make_read_request()
    {
        $path = 'somePath';
        $body = null;
        $expectedData = ['some' => 'json'];

        $this->vaultClient->withConfig($this->vaultConfig);

        $this->setRequestMocks('GET', $path, $body, $expectedData);

        $data = $this->vaultClient->read('somePath');

        $this->assertEquals($expectedData, $data);
    }

    /**
     * @param $method
     * @param $path
     * @param $body
     * @param $expectedData
     */
    protected function setRequestMocks($method = 'POST', $path, $body, $expectedData)
    {
        $this->clientMock->shouldReceive('request')
            ->with($method,
                "{$this->vaultConfig->getAddress()}/v1/{$path}",
                [
                    'headers' => [
                        'X-VAULT-TOKEN' => $this->vaultConfig->getAuthentication()->getToken($this->vaultClient),
                        'Host'          => $this->vaultConfig->getHostName(),
                    ],
                    'json'    => $body,
                ]
            )
            ->andReturnSelf()
            ->once();

        $this->clientMock->shouldReceive('getBody')
            ->andReturn(json_encode($expectedData))
            ->once();
    }
}