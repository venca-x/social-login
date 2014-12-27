<?php

namespace Vencax;

use Nette;


class BaseLogin extends Nette\Object
{

    /** @var Nette\Http\Response */
    protected $httpResponse;

    /** @var Nette\Http\Request */
    protected $httpRequest;

    const SOCIAL_COOKIE_NAME = "cinch-social-login";

    /**
     * Set cookie with social login service
     * @param $socialServiceName Social service name
     */
    protected function setSocialLoginCookie( $socialServiceName )
    {
        $this->httpResponse->setCookie( self::SOCIAL_COOKIE_NAME, $socialServiceName, 0 );
    }

    /**
     * Get cookie with last cosial login
     * @return mixed
     */
    public function getSocialLoginCookie()
    {
        return $this->httpRequest->getCookie( self::SOCIAL_COOKIE_NAME );
    }

}