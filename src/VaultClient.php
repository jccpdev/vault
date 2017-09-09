<?php

namespace Vault;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class VaultClient
{

    /** @var  VaultConfig */
    protected $config;

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param VaultConfig $config
     */
    public function withConfig(VaultConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return VaultConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function write($path, $body = [])
    {
        return $this->makeRequest('POST', $path, $body);
    }

    public function authEnable($type)
    {
        return $this->write("sys/auth/$type", ["type" => $type]);
    }

    public function authDisable($type)
    {
        return $this->makeRequest("DELETE", "sys/auth/$type", ["type" => $type]);
    }

    public function read($path)
    {
        return $this->makeRequest('GET', $path);
    }

    private function makeRequest($method, $path, $body = [])
    {
        $options = [
            'headers' => [
                'X-VAULT-TOKEN' => $this->config->getAuthentication()->getToken($this),
            ],
            'json'    => $body,
        ];

        if (!empty($this->config->getHostName())) {
            $options['headers']['Host'] = $this->config->getHostName();
        }

        $response = $this->client
            ->request($method, $this->config->getAddress() . '/v1/' . $path, $options);

        try {
            $data = json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $data = json_decode($response->getBody(), true);

            throw new \Exception(implode($data['errors'], "\n"));
        }
        return $data;
    }

}
