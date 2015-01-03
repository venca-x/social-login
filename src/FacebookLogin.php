<?php

namespace Vencax;

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;

use Nette;
use Facebook;
use Exception;

/**
 * Login with Facebook API
 * Class FacebookLogin
 * @package Vencax
 */
class FacebookLogin extends BaseLogin
{

    const SOCIAL_NAME = "facebook";

    /** @var FacebookRedirectLoginHelper */
    private $helper;

    /** @var string scope */
    private $scope = "";

    /**
     * @param $params array - data from config.neon
     * @param $cookieName String cookie name
     * @param Nette\Http\Response $httpResponse
     * @param Nette\Http\Request $httpRequest
     */
    public function __construct( $params, $cookieName, Nette\Http\Response $httpResponse, Nette\Http\Request $httpRequest )
    {
        $this->params = $params;
        $this->cookieName = $cookieName;
        $this->httpResponse = $httpResponse;
        $this->httpRequest = $httpRequest;

        FacebookSession::setDefaultApplication( $this->params["appId"], $this->params["appSecret"] );
        $this->helper = new FacebookRedirectLoginHelper( $this->params["callbackURL"] );
    }

    /**
     * Set scope
     * @param $scope
     */
    public function setScope( $scope )
    {
        $this->scope = $scope;
    }

    /**
     * Get URL for login
     * @param string $callbackURL
     * @return string URL login
     */
    public function getLoginUrl()
    {
        $loginUrl = $this->helper->getLoginUrl( array( 'scope' => $this->scope ) );
        return $loginUrl;
    }

    /**
     * Return info about login user
     * @return array
     * @throws Exception
     */
    public function getMe()
    {

        try {
            $session = $this->helper->getSessionFromRedirect();

            $me = ( new Facebook\FacebookRequest( $session, 'GET', '/me' ) )
                ->execute()
                ->getGraphObject( Facebook\GraphUser::className() );

            $me = $me->asArray();//convert to array

            $this->setSocialLoginCookie( self::SOCIAL_NAME );

            return $me;
        }
        catch ( FacebookRequestException $e )
        {
            throw new Exception( $e->getMessage() );
        }
        catch ( Nette\Security\AuthenticationException $e )
        {
            throw new Exception( $e->getMessage() );
        }
        catch ( Exception $e )
        {
            throw new Exception( $e->getMessage() );
        }
    }

    /**
     * Is user last login with this service<
     * @return bool
     */
    public function isThisServiceLastLogin()
    {
        if( $this->getSocialLoginCookie() == self::SOCIAL_NAME )
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

}