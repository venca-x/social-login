<?php
declare(strict_types=1);

namespace VencaX;

use Nette;
use VencaX;

class SocialLogin extends BaseLogin
{
	/** @var VencaX\FacebookLogin */
	public $facebook;

	/** @var VencaX\GoogleLogin */
	public $google;

	/** @var VencaX\Twitter */
	public $twitter;


	/**
	 * @param $params params from cnofig.neon
	 * @param $params $cookieName cookie name - save last used service for login
	 * @param Nette\Http\IResponse $httpResponse
	 * @param Nette\Http\IRequest $httpRequest
	 * @param Nette\Http\Session $session
	 */
	public function __construct($params, $cookieName, Nette\Http\IResponse $httpResponse, Nette\Http\IRequest $httpRequest, Nette\Http\Session $session)
	{
		$this->params = $params;
		$this->cookieName = $cookieName;
		$this->httpResponse = $httpResponse;
		$this->httpRequest = $httpRequest;

		if (isset($this->params['facebook']) && count($this->params['facebook']) > 0) {
			$this->facebook = new FacebookLogin($this->params['facebook'], $this->cookieName, $this->httpResponse, $this->httpRequest);
		}

		if (isset($this->params['google']) && count($this->params['google']) > 0) {
			$this->google = new GoogleLogin($this->params['google'], $cookieName, $this->httpResponse, $this->httpRequest);
		}

		if (isset($this->params['twitter']) && count($this->params['twitter']) > 0) {
			$this->twitter = new TwitterLogin($this->params['twitter'], $cookieName, $session, $this->httpResponse, $this->httpRequest);
		}
	}
}
