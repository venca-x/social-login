<?php
declare(strict_types=1);

namespace Test;

use Tester;
use Tester\Assert;
use VencaX;

$container = require __DIR__ . '/bootstrap.php';

class InstanceTest extends Tester\TestCase
{

	/** @var VencaX\SocialLogin */
	private $socialLogin;

	public function setUp()
	{
		$container = require __DIR__ . '/bootstrap.php';
		$this->socialLogin = $container->getByType('VencaX\SocialLogin');
	}


	public function testFacebookLoginUrl()
	{
		$url = $this->socialLogin->facebook->getLoginUrl();

		$urlParseArray = parse_url($url);
		parse_str($urlParseArray['query'], $urlParseQueryArray);

		Assert::same('https', $urlParseArray['scheme']);
		Assert::same('www.facebook.com', $urlParseArray['host']);
		Assert::same('/v2.11/dialog/oauth', $urlParseArray['path']);

		Assert::same('123456789', $urlParseQueryArray['client_id']);
		Assert::same('code', $urlParseQueryArray['response_type']);
		Assert::same('http://www.muj-web.cz/homepage/facebook-login', $urlParseQueryArray['redirect_uri']);
		//Assert::same('', $urlParseQueryArray['scope']);
	}

	public function testGoogleLoginUrl()
	{
		$url = $this->socialLogin->google->getLoginUrl();

		$urlParseArray = parse_url($url);
		parse_str($urlParseArray['query'], $urlParseQueryArray);

		Assert::same('https', $urlParseArray['scheme']);
		Assert::same('accounts.google.com', $urlParseArray['host']);
		Assert::same('/o/oauth2/auth', $urlParseArray['path']);

		Assert::same('code', $urlParseQueryArray['response_type']);
		Assert::same('online', $urlParseQueryArray['access_type']);
		Assert::same('123456789', $urlParseQueryArray['client_id']);
		Assert::same('http://www.muj-web.cz/homepage/google-login', $urlParseQueryArray['redirect_uri']);
	}

	public function testTwitterLoginUrl()
	{
		//$url = $this->socialLogin->twitter->getLoginUrl();
		Assert::same(true, true);
	}
}

$test = new InstanceTest();
$test->run();
