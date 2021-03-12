<?php
declare(strict_types=1);

namespace Test;

use Abraham;
use Nette;
use Tester;
use Tester\Assert;

require __DIR__ . '/../../vendor/autoload.php';

class InstanceTest extends Tester\TestCase
{
	private $container;

	private $socialLogin;


	public function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}


	public function setUp()
	{
		$this->socialLogin = $this->container->getByType('VencaX\SocialLogin');
	}


	public function testFacebookLoginUrl()
	{
		$this->socialLogin->facebook->setScope(['email']);
		$this->socialLogin->facebook->setState('https://www.mypage.cz/sign/facebook');

		$url = $this->socialLogin->facebook->getLoginUrl();

		$urlParseArray = parse_url($url);
		parse_str($urlParseArray['query'], $urlParseQueryArray);

		//Assert::same(false, $this->socialLogin->facebook->getSocialLoginCookie());
		Assert::same(false, $this->socialLogin->facebook->isThisServiceLastLogin());

		Assert::same('https', $urlParseArray['scheme']);
		Assert::same('www.facebook.com', $urlParseArray['host']);
		Assert::same('/v8.0/dialog/oauth', $urlParseArray['path']);

		Assert::same('123456789', $urlParseQueryArray['client_id']);
		Assert::same('code', $urlParseQueryArray['response_type']);
		Assert::same('http://www.muj-web.cz/homepage/facebook-login', $urlParseQueryArray['redirect_uri']);
		Assert::same('email', $urlParseQueryArray['scope']);
		Assert::same('https://www.mypage.cz/sign/facebook', $urlParseQueryArray['state']);
	}


	public function testGoogleLoginUrl()
	{
		$this->socialLogin->google->setScope(['https://www.googleapis.com/auth/plus.me', 'https://www.googleapis.com/auth/userinfo.email']);
		$this->socialLogin->google->setState('https://www.mypage.cz/sign/google');

		$url = $this->socialLogin->google->getLoginUrl();

		$urlParseArray = parse_url($url);
		parse_str($urlParseArray['query'], $urlParseQueryArray);

		//Assert::same(false, $this->socialLogin->google->getSocialLoginCookie());
		Assert::same(false, $this->socialLogin->google->isThisServiceLastLogin());

		Assert::same('https', $urlParseArray['scheme']);
		Assert::same('accounts.google.com', $urlParseArray['host']);
		Assert::same('/o/oauth2/auth', $urlParseArray['path']);

		Assert::same('code', $urlParseQueryArray['response_type']);
		Assert::same('online', $urlParseQueryArray['access_type']);
		Assert::same('123456789', $urlParseQueryArray['client_id']);
		Assert::same('http://www.muj-web.cz/homepage/google-login', $urlParseQueryArray['redirect_uri']);
		Assert::same('https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/userinfo.email', $urlParseQueryArray['scope']);
		Assert::same('https://www.mypage.cz/sign/google', $urlParseQueryArray['state']);
	}


	public function testTwitterLoginUrl()
	{
		Assert::exception(function () {
			$this->socialLogin->twitter->getLoginUrl();
		}, Abraham\TwitterOAuth\TwitterOAuthException::class, '{"errors":[{"code":32,"message":"Could not authenticate you."}]}');

		Assert::exception(function () {
			$this->socialLogin->twitter->getMe('oauthToken', 'oauthVerifier');
		}, \Exception::class, 'Twitter token is old. Try again login');

		Assert::same(false, $this->socialLogin->twitter->isThisServiceLastLogin());
	}
}

require __DIR__ . '/Bootstrap.php';

$container = \Test\Bootstrap::bootForTests()
	->createContainer();

$test = new InstanceTest($container);
$test->run();
