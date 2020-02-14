<?php
namespace App\Auth;
use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\Authenticatable as User;

class StatelessTokenGuard extends TokenGuard {
	
	protected $tokenLifetime = 864000; //10 days
	
	protected $encryptionKey;
	
	public function __construct(
			\Illuminate\Contracts\Auth\UserProvider $provider,
			\Illuminate\Http\Request $request,
			$tokenLifetime = null,
			$storageKey = 'id'
	) {
		parent::__construct($provider, $request);
		
		if ($tokenLifetime) {
			$this->setTokenLifetime($tokenLifetime);
		}
		
		$this->setStorageKey($storageKey);
	}
	
	protected function getEncryptionKey() {
		return str_pad(substr(config('app.key'), 7), 62, 'z');
	}
	
	protected function encrypt($plain) {
		//return base64_encode(\Crypt::encryptString($plain));
		
		//faster with xor (less secure)
		return base64_encode($plain ^ $this->getEncryptionKey());
	}
	
	public function decrypt($cipher) {
		
		/*$plain = '';
		
		try {
			$plain = \Crypt::decryptString(base64_decode($cipher));
			
		} catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
			
		}*/
		
		//faster with xor (less secure)
		$plain = base64_decode($cipher) ^ $this->getEncryptionKey();
		return $plain;
	}
	
	public function generateToken(User $user = null) {
		
		if ($user === null) {
			$user = $this->user;
		}
		
		$plain = implode(':', array_values([
			'refresh_token' => str_random(20),
			'id' => str_pad($user->getAuthIdentifier(), 30),
			'timestamp' => time(),
		]));
		
		$encrypted = $this->encrypt($plain);
		
		return $encrypted;
	}
	
	public function parseToken($token) {
		$plain = $this->decrypt($token);
		
		$partsRaw = explode(':', $plain);
		
		if (count($partsRaw) != 3) {
			return null;
		}
		
		$refreshToken = $partsRaw[0];
		
		if (!preg_match('/^([0-9a-zA-Z]{20})$/', $refreshToken)) {
			return null;
		}
		
		$id = trim($partsRaw[1]);
		if (empty($id)) {
			return null;
		}
		
		$timestamp = $partsRaw[2];
		if (!preg_match('/^([1-9][0-9]{9})$/', $timestamp)) {
			return null;
		}
		
		return [
			'id' => $id,
			'timestamp' => $timestamp,
			'refresh_token' => $refreshToken
		];
	}
	
	public function getRefreshToken($token) {
		
		$tokenParts = $this->parseToken($token);
		
		if ($tokenParts) {
			return $tokenParts['refresh_token'];
		}
		
		return null;
	}
	
	public function validateRefreshToken($refreshToken, $token = null) {
		
		if (empty($refreshToken)) {
			return false;
		}
		
		if ($token === null) {
			$token = $this->getTokenForRequest();
		}
		
		$realRefreshToken = $this->getRefreshToken($token);
		
		if (empty($realRefreshToken)) {
			return false;
		}
		
		if($refreshToken !== $realRefreshToken) {
			return false;
		}
		
		return true;
	}
	
	public function user() {
		// If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (! is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        $token = $this->getTokenForRequest();
		
		$tokenParts = $this->parseToken($token);
		
		if (empty($tokenParts)) {
			//token invalid
			return $this->user = $user;
		}
		
		$tokenLifetime = $this->getTokenLifetime();
		
		if ($tokenLifetime > 0 && time() - $tokenParts['timestamp'] > $tokenLifetime) {
			//token expired
			return $this->user = $user;
		}
		
		$user = $this->provider->retrieveByCredentials(
			[$this->storageKey => $tokenParts['id']]
		);

        return $this->user = $user;
	}
	
	public function attempt($credentials) {
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
	
	public function attemptRefreshToken($refreshToken, $token = null) {
		if ($token === null) {
			$token = $this->getTokenForRequest();
		}
		
		if (!$this->validateRefreshToken($refreshToken, $token)) {
			return false;
		}
		
		$tokenParts = $this->parseToken($token);
		
		if (!$tokenParts) {
			return false;
		}
		
		$user = $this->provider->retrieveByCredentials(
			[$this->storageKey => $tokenParts['id']]
		);

        return $this->user = $user;
	}
	
	function getTokenLifetime() {
		return $this->tokenLifetime;
	}
	
	function setTokenLifetime($tokenLifetime) {
		$this->tokenLifetime = (int) $tokenLifetime;
		
		return $this;
	}
	
	public function getStorageKey() {
		return $this->storageKey;
	}
	
	public function setStorageKey($storageKey) {
		if (empty($storageKey)) {
			throw new \InvalidArgumentException('Argument $storageKey cant be empty');
		}
		
		$this->storageKey = $storageKey;
		
		return $this;
	}
}