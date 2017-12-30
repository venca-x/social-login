<?php
declare(strict_types=1);

namespace VencaX;

use Exception;
use Facebook;
use Facebook\FacebookRedirectLoginHelper;

use Nette;

/**
 * Login with Facebook API
 * Class FacebookLogin
 * @package VencaX
 */
class FacebookLogin extends BaseLogin
{

	//me permissions
	public const ID = 'id';

	public const ABOUT = 'about';

	public const ADMIN_NOTES = 'admin_notes';

	public const AGE_RANGE = 'age_range';

	public const BIO = 'bio';

	public const BIRTHDAY = 'birthday';

	public const CONTEXT = 'context';

	public const COVER = 'cover';

	public const CURRENCY = 'currency';

	public const DEVICES = 'devices';

	public const EDUCATION = 'education';

	public const EMAIL = 'email';

	public const FAVORITE_ATHLETES = 'favorite_athletes';

	public const FAVORITE_TEAMS = 'favorite_teams';

	public const FIRST_NAME = 'first_name';

	public const GENDER = 'gender';

	public const HOMETOWN = 'hometown';

	public const INSPIRATIONAL_PEOPLE = 'inspirational_people';

	public const INSTALL_TYPE = 'install_type';

	public const INSTALLED = 'installed';

	public const INTERESTED_IN = 'interested_in';

	public const IS_SHARED_LOGIN = 'is_shared_login';

	public const IS_VERIFIED = 'is_verified';

	public const LABELS = 'labels';

	public const LANGUAGES = 'languages';

	public const LAST_NAME = 'last_name';

	public const LINK = 'link';

	public const LOCALE = 'locale';

	public const LOCATION = 'location';

	public const MEETING_FOR = 'meeting_for';

	public const MIDDLE_NAME = 'middle_name';

	public const NAME = 'name';

	public const NAME_FORMAT = 'name_format';

	public const PAYMENT_PRICEPOINTS = 'payment_pricepoints';

	public const POLITICAL = 'political';

	public const PUBLIC_KEY = 'public_key';

	public const QUOTES = 'quotes';

	public const RELATIONSHIP_STATUS = 'relationship_status';

	public const RELIGION = 'religion';

	public const SECURITY_SETTINGS = 'security_settings';

	public const SHARED_LOGIN_UPGRADE_REQUIRED_BY = 'shared_login_upgrade_required_by';

	public const SIGNIFICANT_OTHER = 'significant_other';

	public const SPORTS = 'sports';

	public const TEST_GROUP = 'test_group';

	public const THIRD_PARTY_ID = 'third_party_id';

	public const TIMEZONE = 'timezone';

	public const TOKEN_FOR_BUSINESS = 'token_for_business';

	public const UPDATED_TIME = 'updated_time';

	public const VERIFIED = 'verified';

	public const VIDEO_UPLOAD_LIMITS = 'video_upload_limits';

	public const VIEWER_CAN_SEND_GIFT = 'viewer_can_send_gift';

	public const WEBSITE = 'website';

	public const WORK = 'work';

	public const SOCIAL_NAME = 'facebook';

	public const DEFAULT_FB_GRAPH_VERSION = 'v2.11';

	/** @var Facebook\Facebook */
	private $fb;

	/** @var FacebookRedirectLoginHelper */
	private $helper;

	/** @var string scope */
	private $scope = [];

	/** @var string callBackUrl */
	private $callBackUrl = '';


	/**
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
		$this->callBackUrl = $this->params['callbackURL'];

		$default_graph_version = self::DEFAULT_FB_GRAPH_VERSION;
		if (array_key_exists ('defaultFbGraphVersion', $this->params) && $this->params['defaultFbGraphVersion'] != '') {
			//set users defaultFbGraphVersion
			$default_graph_version = $this->params['defaultFbGraphVersion'];
		}

		$this->fb = new Facebook\Facebook([
			'app_id' => $this->params['appId'],
			'app_secret' => $this->params['appSecret'],
			'default_graph_version' => $default_graph_version,
		]);
		$this->helper = $this->fb->getRedirectLoginHelper();
	}


	/**
	 * Set scope
	 * @param $scope
	 */
	public function setScope($scope)
	{
		$this->scope = $scope;
	}


	/**
	 * Set state
	 * @param string $state
	 */
	public function setState($state)
	{
		$this->helper->getPersistentDataHandler()->set('state', $state);
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
	public function getMe($fields)
	{
		$accessTokenObject = $this->helper->getAccessToken($this->callBackUrl);
		if ($accessTokenObject == null) {
			throw new Exception('User not allowed permissions');
		}

		if ($fields == '' || !is_array($fields) || count($fields) == 0) {
			//array is empty
			$fields = [self::ID];//set ID field
		}

		try {
			if (isset($_SESSION['facebook_access_token'])) {
				$this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
			} else {
				$_SESSION['facebook_access_token'] = (string) $accessTokenObject;
			}

			$client = $this->fb->getOAuth2Client();
			$accessToken = $client->getLongLivedAccessToken($accessTokenObject->getValue());
			$response = $this->fb->get('/me?fields=' . implode(',', $fields), $accessToken);

			$this->setSocialLoginCookie(self::SOCIAL_NAME);

			return $response->getDecodedBody();

		} catch (Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			throw new Exception($e->getMessage());
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			throw new Exception($e->getMessage());
		}
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
