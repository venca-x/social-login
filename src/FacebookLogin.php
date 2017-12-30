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

    //me permissions
    const ID = "id";
    const ABOUT = "about";
    const ADMIN_NOTES = "admin_notes";
    const AGE_RANGE = "age_range";
    const BIO = "bio";
    const BIRTHDAY = "birthday";
    const CONTEXT = "context";
    const COVER = "cover";
    const CURRENCY = "currency";
    const DEVICES = "devices";
    const EDUCATION = "education";
    const EMAIL = "email";
    const FAVORITE_ATHLETES = "favorite_athletes";
    const FAVORITE_TEAMS = "favorite_teams";
    const FIRST_NAME = "first_name";
    const GENDER = "gender";
    const HOMETOWN = "hometown";
    const INSPIRATIONAL_PEOPLE = "inspirational_people";
    const INSTALL_TYPE = "install_type";
    const INSTALLED = "installed";
    const INTERESTED_IN = "interested_in";
    const IS_SHARED_LOGIN = "is_shared_login";
    const IS_VERIFIED = "is_verified";
    const LABELS = "labels";
    const LANGUAGES = "languages";
    const LAST_NAME = "last_name";
    const LINK = "link";
    const LOCALE = "locale";
    const LOCATION = "location";
    const MEETING_FOR = "meeting_for";
    const MIDDLE_NAME = "middle_name";
    const NAME = "name";
    const NAME_FORMAT = "name_format";
    const PAYMENT_PRICEPOINTS = "payment_pricepoints";
    const POLITICAL = "political";
    const PUBLIC_KEY = "public_key";
    const QUOTES = "quotes";
    const RELATIONSHIP_STATUS = "relationship_status";
    const RELIGION = "religion";
    const SECURITY_SETTINGS = "security_settings";
    const SHARED_LOGIN_UPGRADE_REQUIRED_BY = "shared_login_upgrade_required_by";
    const SIGNIFICANT_OTHER = "significant_other";
    const SPORTS = "sports";
    const TEST_GROUP = "test_group";
    const THIRD_PARTY_ID = "third_party_id";
    const TIMEZONE = "timezone";
    const TOKEN_FOR_BUSINESS = "token_for_business";
    const UPDATED_TIME = "updated_time";
    const VERIFIED = "verified";
    const VIDEO_UPLOAD_LIMITS = "video_upload_limits";
    const VIEWER_CAN_SEND_GIFT = "viewer_can_send_gift";
    const WEBSITE = "website";
    const WORK = "work";

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
            'default_graph_version' => 'v2.6',
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
     * Set state
     * @param string $state
     */
    public function setState( $state )
    {
        $this->helper->getPersistentDataHandler()->set( 'state', $state );
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
     * Return info about facebook user
     * @param $fields
     * @return array
     * @throws Exception
     */
    public function getMe( $fields )
    {
        $client = $this->fb->getOAuth2Client();
        $accessTokenObject = $this->helper->getAccessToken($this->callBackUrl);
        if($accessTokenObject == null) {
            throw new Exception( "User not allowed permissions");
        }

        if($fields == "" || !is_array($fields) || count($fields) == 0 ) {
            //array is empty
            $fields = array( ID );//set ID field
        }

        try {
            $accessToken = $client->getLongLivedAccessToken($accessTokenObject->getValue());
            $response = $this->fb->get("/me?fields=" . implode(",", $fields), $accessToken);

            $this->setSocialLoginCookie( self::SOCIAL_NAME );

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