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


	public function __construct(
		$params,
		$cookieName,
		Nette\Http\IResponse $httpResponse,
		Nette\Http\IRequest $httpRequest,
		Nette\Http\Session $session
	) {
		$this->params = $params;
		$this->cookieName = $cookieName;
		$this->httpResponse = $httpResponse;
		$this->httpRequest = $httpRequest;

		if ($this->existParamArray(@$this->params['facebook'])) {
			$this->facebook = new FacebookLogin($this->params['facebook'], $this->cookieName, $this->httpResponse, $this->httpRequest);
		}

		if ($this->existParamArray(@$this->params['google'])) {
			$this->google = new GoogleLogin($this->params['google'], $cookieName, $this->httpResponse, $this->httpRequest);
		}

		if ($this->existParamArray(@$this->params['twitter'])) {
			$this->twitter = new TwitterLogin($this->params['twitter'], $cookieName, $session, $this->httpResponse, $this->httpRequest);
		}
	}


	private function existParamArray($param)
	{
		return is_array($param) && count($param) > 0;
	}
}
