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

    /** @var Facebook\Facebook */
    private $fb;

    /** @var FacebookRedirectLoginHelper */
    private $helper;

    /** @var string scope */
    private $scope = array();

    /** @var string callBackUrl */
    private $callBackUrl = "";

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
        $this->callBackUrl = $this->params["callbackURL"];

        $this->fb = new Facebook\Facebook([
            'app_id'     => $this->params["appId"],
            'app_secret' => $this->params["appSecret"],
            'default_graph_version' => 'v2.5',
        ]);
        $this->helper = $this->fb->getRedirectLoginHelper();
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
        $loginUrl = $this->helper->getLoginUrl($this->callBackUrl, $this->scope);
        return $loginUrl;
    }

    /**
     * Return info about login user
     * @return array
     * @throws Exception
     */
    public function getMe()
    {
        $client = $this->fb->getOAuth2Client();
        try {
            $accessToken = $client->getLongLivedAccessToken($this->helper->getAccessToken()->getValue());
            $response = $this->fb->get('/me?fields=id,name,email', $accessToken);

            return $response->getDecodedBody();
            
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            throw new Exception( $e->getMessage() );
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
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