[![Build Status](https://travis-ci.org/jccpdev/vault.svg?branch=master)](https://travis-ci.org/jccpdev/vault)

# Vault SDK

Supports:

- auth
	- tokens
	- authrole
- general
	- read
	- write


## Examples

### Configure Environment

The default client will leverage the environment variables `VAULT_ADDR` and `VAULT_TOKEN`

	export VAULT_ADDR=http://localhost:8200
	export VAULT_TOKEN=horde

### Read and Write Secrets

	$secrets = [
		"foo" => "bar",
		"baz" => "boo",
	];

	$config = new DefaultVaultConfig('http://localhost:8200', new Tokens('horde'), '');

    $vaultClient = new VaultClient(new Client());
    $vaultClient->withConfig($config);
    $vaultClient->write('secret/testing', $secrets);
    $found = $vaultClient->read('secret/testing');

	print_r($found['data']);
	
	// Output:
	// Array
	// (
	//     [baz] => boo
	//     [foo] => bar
	// )

### Login with AppRole
	
	$roleId = "...";
	$secretId = "...";
	$secrets = [
		"foo" => "bar",
		"baz" => "boo",
	];

    $vaultClient = new VaultClient(new Client());
    
    $auth = new AppRole($vaultClient);
    $auth->with('theRoleId', 'theSecretId');
    
    $config = new DefaultVaultConfig('http://localhost:8200', $auth, '');
    
    $vaultClient->withConfig($config);
    $vaultClient->write('secret/testing', $secrets);
    
    $found = $vaultClient->read('secret/testing');
	print_r($found['data']);
	
	// Output:
	// Array
	// (
	//     [baz] => boo
	//     [foo] => bar
	// )

### Credit
This is a fork of https://github.com/fliglio/vault