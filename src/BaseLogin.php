<?php
declare(strict_types=1);

namespace VencaX;

use Nette;


class BaseLogin extends Nette\Object
{
	/** @var array params */
	protected $params;

	/** @var String cookie name - save last used service for login */
	protected $cookieName;

	/** @var Nette\Http\IResponse */
	protected $httpResponse;

	/** @var Nette\Http\IRequest */
	protected $httpRequest;


	/**
	 * Set cookie with social login service
	 * @param $socialServiceName Social service name for
	 */
	protected function setSocialLoginCookie($socialServiceName)
	{
		$this->httpResponse->setCookie($this->cookieName, $socialServiceName, 0);
	}


	/**
	 * Get cookie with last cosial login
	 * @return mixed
	 */
	public function getSocialLoginCookie()
	{
		return $this->httpRequest->getCookie($this->cookieName);
	}
}
