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
	/**
	 * Deprecated
	 */
	public const ID = 'id';

	/**
	 * Deprecated
	 */
	public const ABOUT = 'about';

	/**
	 * Deprecated
	 */
	public const ADMIN_NOTES = 'admin_notes';

	/**
	 * Deprecated
	 */
	public const AGE_RANGE = 'age_range';

	/**
	 * Deprecated
	 */
	public const BIO = 'bio';

	/**
	 * Deprecated
	 */
	public const BIRTHDAY = 'birthday';

	/**
	 * Deprecated
	 */
	public const CONTEXT = 'context';

	/**
	 * Deprecated
	 */
	public const COVER = 'cover';

	/**
	 * Deprecated
	 */
	public const CURRENCY = 'currency';

	/**
	 * Deprecated
	 */
	public const DEVICES = 'devices';

	/**
	 * Deprecated
	 */
	public const EDUCATION = 'education';

	/**
	 * Deprecated
	 */
	public const EMAIL = 'email';

	/**
	 * Deprecated
	 */
	public const FAVORITE_ATHLETES = 'favorite_athletes';

	/**
	 * Deprecated
	 */
	public const FAVORITE_TEAMS = 'favorite_teams';

	/**
	 * Deprecated
	 */
	public const FIRST_NAME = 'first_name';

	/**
	 * Deprecated
	 */
	public const GENDER = 'gender';

	/**
	 * Deprecated
	 */
	public const HOMETOWN = 'hometown';

	/**
	 * Deprecated
	 */
	public const INSPIRATIONAL_PEOPLE = 'inspirational_people';

	/**
	 * Deprecated
	 */
	public const INSTALL_TYPE = 'install_type';

	/**
	 * Deprecated
	 */
	public const INSTALLED = 'installed';

	/**
	 * Deprecated
	 */
	public const INTERESTED_IN = 'interested_in';

	/**
	 * Deprecated
	 */
	public const IS_SHARED_LOGIN = 'is_shared_login';

	/**
	 * Deprecated
	 */
	public const IS_VERIFIED = 'is_verified';

	/**
	 * Deprecated
	 */
	public const LABELS = 'labels';

	/**
	 * Deprecated
	 */
	public const LANGUAGES = 'languages';

	/**
	 * Deprecated
	 */
	public const LAST_NAME = 'last_name';

	/**
	 * Deprecated
	 */
	public const LINK = 'link';

	/**
	 * Deprecated
	 */
	public const LOCALE = 'locale';

	/**
	 * Deprecated
	 */
	public const LOCATION = 'location';

	/**
	 * Deprecated
	 */
	public const MEETING_FOR = 'meeting_for';

	/**
	 * Deprecated
	 */
	public const MIDDLE_NAME = 'middle_name';

	/**
	 * Deprecated
	 */
	public const NAME = 'name';

	/**
	 * Deprecated
	 */
	public const NAME_FORMAT = 'name_format';

	/**
	 * Deprecated
	 */
	public const PAYMENT_PRICEPOINTS = 'payment_pricepoints';

	/**
	 * Deprecated
	 */
	public const POLITICAL = 'political';

	/**
	 * Deprecated
	 */
	public const PUBLIC_KEY = 'public_key';

	/**
	 * Deprecated
	 */
	public const QUOTES = 'quotes';

	/**
	 * Deprecated
	 */
	public const RELATIONSHIP_STATUS = 'relationship_status';

	/**
	 * Deprecated
	 */
	public const RELIGION = 'religion';

	/**
	 * Deprecated
	 */
	public const SECURITY_SETTINGS = 'security_settings';

	/**
	 * Deprecated
	 */
	public const SHARED_LOGIN_UPGRADE_REQUIRED_BY = 'shared_login_upgrade_required_by';

	/**
	 * Deprecated
	 */
	public const SIGNIFICANT_OTHER = 'significant_other';

	/**
	 * Deprecated
	 */
	public const SPORTS = 'sports';

	/**
	 * Deprecated
	 */
	public const TEST_GROUP = 'test_group';

	/**
	 * Deprecated
	 */
	public const THIRD_PARTY_ID = 'third_party_id';

	/**
	 * Deprecated
	 */
	public const TIMEZONE = 'timezone';

	/**
	 * Deprecated
	 */
	public const TOKEN_FOR_BUSINESS = 'token_for_business';

	/**
	 * Deprecated
	 */
	public const UPDATED_TIME = 'updated_time';

	/**
	 * Deprecated
	 */
	public const VERIFIED = 'verified';

	/**
	 * Deprecated
	 */
	public const VIDEO_UPLOAD_LIMITS = 'video_upload_limits';

	/**
	 * Deprecated
	 */
	public const VIEWER_CAN_SEND_GIFT = 'viewer_can_send_gift';

	/**
	 * Deprecated
	 */
	public const WEBSITE = 'website';

	/**
	 * Deprecated
	 */
	public const WORK = 'work';

	public const SOCIAL_NAME = 'facebook';

	public const DEFAULT_FB_GRAPH_VERSION = 'v10.0';

	/** @var League\OAuth2\Client\Provider\Facebook */
	private $provider;

	/** @var string scope */
	private $scope = [];

	/** @var string state */
	private $state;

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
	 * Set state
	 * @param string $state
	 */
	public function setState($state)
	{
		$this->state = $state;
	}


	/**
	 * Get URL for login
	 * @return string
	 */
	public function getLoginUrl()
	{
		$options = ['scope' => $this->scope];
		if ($this->state != null) {
			$options['state'] = $this->state;
		}
		$loginUrl = $this->provider->getAuthorizationUrl($options);
		$_SESSION['oauth2state'] = $this->provider->getState();
		return $loginUrl;
	}


	/**
	 * Return info about facebook user
	 * @param $fields Deprecated: this parameter is not necessary - return whole array
	 * @return array
	 * @throws League\OAuth2\Client\Provider\Exception\IdentityProviderException
	 */
	public function getMe($fields = null)
	{
		$state = $this->httpRequest->getQuery('state');
		if ($state !== null && $state == $_SESSION['oauth2state']) {

			// Try to get an access token (using the authorization code grant)
			$token = $this->provider->getAccessToken('authorization_code', [
				'code' => $this->httpRequest->getQuery('code'),
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
			if (in_array('oauth2state', $_SESSION, true)) {
				unset($_SESSION['oauth2state']);
			}
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
