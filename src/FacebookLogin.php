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

    //Facebook
    public function __construct( $params )
    {
        FacebookSession::setDefaultApplication( $params["appId"], $params["appSecret"] );
        $this->helper = new FacebookRedirectLoginHelper( $params["callbackURL"] );
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
     * @return mixed
     * @throws Exception
     */
    public function getMe()
    {

        try {
            $session = $this->helper->getSessionFromRedirect();

            $me = ( new Facebook\FacebookRequest( $session, 'GET', '/me' ) )
                ->execute()
                ->getGraphObject( Facebook\GraphUser::className() );

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