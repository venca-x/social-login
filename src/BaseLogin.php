<?php

namespace Vencax;

use Nette;


class BaseLogin extends Nette\Object
{

    const SOCIAL_COOKIE_NAME = "cinch-social-login";

    /**
     * Set cookie with social login service
     * @param $socialServiceName Social service name
     */
    protected function setSocialLoginCookie( $socialServiceName )
    {
        $this->getHttpResponse()->setCookie( self::SOCIAL_COOKIE_NAME, $socialServiceName, 0 );
    }

    /**
     * Get cookie with last cosial login
     * @return mixed
     */
    public function getSocialLoginCookie()
    {
        return $this->getHttpRequest()->getCookie( self::SOCIAL_COOKIE_NAME );
    }

}