<?php
declare(strict_types=1);

namespace VencaX;

use Exception;
use League;
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

	public const DEFAULT_FB_GRAPH_VERSION = 'v8.0';

	/** @var League\OAuth2\Client\Provider\Facebook */
	private $provider;

	/** @var string scope */
	private $scope = [];

	/** @var string callBackUrl */
	private $callBackUrl = '';


	public function __construct(
		$params,
		$cookieName,
		Nette\Http\IResponse $httpResponse,
		Nette\Http\IRequest $httpRequest
	) {
		$this->params = $params;
		$this->cookieName = $cookieName;
		$this->httpResponse = $httpResponse;
		$this->httpRequest = $httpRequest;
		$this->callBackUrl = $this->params['callbackURL'];

		$default_graph_version = self::DEFAULT_FB_GRAPH_VERSION;
		if (array_key_exists('defaultFbGraphVersion', $this->params) && $this->params['defaultFbGraphVersion'] != '') {
			//set users defaultFbGraphVersion
			$default_graph_version = $this->params['defaultFbGraphVersion'];
		}

		$this->provider = new League\OAuth2\Client\Provider\Facebook([
			'clientId' => $this->params['appId'],
			'clientSecret' => $this->params['appSecret'],
			'redirectUri' => $this->callBackUrl,
			'graphApiVersion' => $default_graph_version,
		]);
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
	 * Get URL for login
	 * @return string
	 */
	public function getLoginUrl()
	{
		$loginUrl = $this->provider->getAuthorizationUrl(['scope' => $this->scope]);
		$_SESSION['oauth2state'] = $this->provider->getState();
		return $loginUrl;
	}


	/**
	 * Return info about facebook user
	 * @param $fields Deprecated: this parameter is not necessary - return whole array
	 * @return array
	 * @throws League\OAuth2\Client\Provider\Exception\IdentityProviderException
	 */
	public function getMe($fields)
	{
		$code = $this->httpRequest->getQuery('code');
		if ($code !== null && $code == $_SESSION['oauth2state']) {

			// Try to get an access token (using the authorization code grant)
			$token = $this->provider->getAccessToken('authorization_code', [
				'code' => $code,
			]);

			try {
				// We got an access token, let's now get the user's details
				$user = $this->provider->getResourceOwner($token);
				return $user->toArray();
			} catch (Exception $e) {
				// Failed to get user details
				throw new Exception('FacebookLogin - token exception: ' . $e->getMessage());
			}

		} else {
			unset($_SESSION['oauth2state']);
			throw new Exception('Invalid state');
		}
	}


	/**
	 * Is user last login with this service<
	 * @return bool
	 */
	public function isThisServiceLastLogin()
	{
		return $this->getSocialLoginCookie() == self::SOCIAL_NAME;
	}
}
