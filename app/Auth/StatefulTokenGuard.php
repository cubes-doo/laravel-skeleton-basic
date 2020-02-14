<?php

namespace App\Auth;

use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\Authenticatable as User;

class StatefulTokenGuard extends TokenGuard 
{
	public function __construct(
		\Illuminate\Contracts\Auth\UserProvider $provider,
		\Illuminate\Http\Request $request,
		$storageKey = 'api_token'
	) {
		parent::__construct($provider, $request);
		
		$this->setStorageKey($storageKey);
	}

    public function generateToken(User $user = null) 
    {
		if ($user === null) {
			$user = $this->user;
		}
		
		return md5($user->getAuthIdentifier() . '_' . time());
	}
	
	public function getRefreshToken(User $user = null) 
	{
		if ($user === null) {
			$user = $this->user;
		}
		
		return md5('refresh_' . $user->getAuthIdentifier() . '_' . time());
	}
    
    public function attempt($credentials) 
    {
		$user = $this->provider->retrieveByCredentials($credentials);
		
		if (!$user) {
			return false;
		}
		
		if (!$this->provider->validateCredentials($user, $credentials)) {
			return false;
		}
		
		$this->user = $user;
		return true;
	}

	public function validateRefreshToken($user, $refreshToken) 
	{
		if (empty($refreshToken)) {
			return false;
		}
		
		if (empty($user->refresh_token)) {
			return false;
		}
		
		if($refreshToken !== $user->refresh_token) {
			return false;
		}
		
		return true;
	}

	public function attemptRefreshToken($refreshToken, $token) 
	{
		if ($token === null) {
			$token = $this->getTokenForRequest();
		}

		$user = $this->provider->retrieveByCredentials(
			[$this->storageKey => $token]
		);
		
		if (!$this->validateRefreshToken($user, $refreshToken)) {
			return false;
		}

        return $this->user = $user;
	}

	public function getStorageKey() 
	{
		return $this->storageKey;
	}
	
	public function setStorageKey($storageKey) 
	{
		if (empty($storageKey)) {
			throw new \InvalidArgumentException('Argument $storageKey cant be empty');
		}
		
		$this->storageKey = $storageKey;
		
		return $this;
	}
}