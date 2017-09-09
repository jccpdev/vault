<?php
namespace Vault\Auth;

use Vault\VaultClient;

class Tokens implements Auth {

	private $token;

	public function __construct($token) {
		$this->token = $token;
	}

	public function getToken() {
		return $this->token;
	}
}
