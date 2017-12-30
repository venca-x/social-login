<?php
declare(strict_types=1);

namespace VencaX;

use Exception;
use Nette;

class GoogleLogin extends BaseLogin
{
	public const SOCIAL_NAME = 'google';

	/** @var Google_Client */
	private $client;

	/** @var array scope */
	private $scope = [];


	/**
	 * Google
	 * @param $params array - data from config.neon
	 * @param $cookieName String cookie name
	 * @param Nette\Http\IResponse $httpResponse
	 * @param Nette\Http\IRequest $httpRequest
	 */
	public function __construct($params, $cookieName, Nette\Http\IResponse $httpResponse, Nette\Http\IRequest $httpRequest)
	{
		$this->params = $params;
		$this->cookieName = $cookieName;
		$this->httpResponse = $httpResponse;
		$this->httpRequest = $httpRequest;

		$this->client = new \Google_Client();

		$this->client->setClientId($this->params['clientId']);
		$this->client->setClientSecret($this->params['clientSecret']);
		$this->client->setRedirectUri($this->params['callbackURL']);
	}


	/**
	 * Set scope
	 * @param array $scope
	 */
	public function setScope(array $scope)
	{
		$this->scope = $scope;
	}

	/**
	 * Set state
	 * @param string $state
	 */
	public function setState( $state )
	{
		$this->helper->getPersistentDataHandler()->set( 'state', $state );
	}


	/**
	 * Get URL for login
	 * @return string
	 */
	public function getLoginUrl()
	{
		$this->client->setScopes($this->scope);

		return $this->client->createAuthUrl();
	}


	/**
	 * Return info about login user
	 * @param $code
	 * @return \Google_Service_Oauth2_Userinfoplus
	 * @throws Exception
	 */
	public function getMe($code)
	{
		$google_oauthV2 = new \Google_Service_Oauth2($this->client);

		try {
			$this->client->authenticate($code);
			$user = $google_oauthV2->userinfo->get();
		} catch (\Google_Auth_Exception $e) {
			throw new Exception($e->getMessage());
		}

		$this->setSocialLoginCookie(self::SOCIAL_NAME);

		return $user;
	}


	/**
	 * Is user last login with this service<
	 * @return bool
	 */
	public function isThisServiceLastLogin()
	{
		if ($this->getSocialLoginCookie() == self::SOCIAL_NAME) {
			return true;
		} else {
			return false;
		}
	}
}
