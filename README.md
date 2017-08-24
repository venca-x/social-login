social-login
===============
 
[![Build Status](https://api.travis-ci.org/venca-x/social-login.svg?branch=v1.0)](https://travis-ci.org/venca-x/social-login) 
[![Latest Stable Version](https://poser.pugx.org/venca-x/social-login/v/stable.svg)](https://packagist.org/packages/venca-x/social-login) 
[![Total Downloads](https://poser.pugx.org/venca-x/social-login/downloads.svg)](https://packagist.org/packages/venca-x/social-login) 
[![Latest Unstable Version](https://poser.pugx.org/venca-x/social-login/v/unstable.svg)](https://packagist.org/packages/venca-x/social-login) 
[![License](https://poser.pugx.org/venca-x/social-login/license.svg)](https://packagist.org/packages/venca-x/social-login)


Nette addon for logint with social networks

Version 1.0.0 use Facebook App API version v 2.6
[All permissions for Facebook fields](https://developers.facebook.com/docs/graph-api/reference/user/)

Branch v 1.0.x is for Nette 2.4
------------

Installation
------------

Add the bundle to your dependencies:
```
composer require venca-x/social-login:~1.0.0
```
 
``` 
Final composer.json:
// composer.json
{
   // ...
   "require": {
       // ...
       "facebook/graph-sdk" : "^5.4",
       "google/apiclient": "~2.0",
       "kertz/twitteroauth": "dev-master",
       "venca-x/social-login": "~1.0.0"
   }
}
```
Configuration
-------------

config.neon
```
	parameters:
		facebook:
			appId: '123456789'
			appSecret: '987654321'
			callbackURL: 'http://www.muj-web.cz/homepage/facebook-login'
		google:
			clientId: '123456789'
			clientSecret: '987654321'
			callbackURL: 'http://www.muj-web.cz/homepage/google-login'
		twitter:
			consumerKey: '123456789'
			consumerSecret: '987654321'
			callbackURL: 'http://www.muj-web.cz/homepage/twitter-login'

	nette:
		session:
			autoStart: true  # default is smart	

    services:
        ...
        - Vencax\SocialLogin({ facebook: %facebook%, google: %google%, twitter: %twitter% }, 'domain-social-login' )
```
Where 'domain-social-login' replace to your unique identifier (it's cookie name for last used services for login)


BasePresenter.php

```php
    use Vencax;

    /** @var Vencax\SocialLogin */
    private $socialLogin;

    public function injectSocialLogin( Vencax\SocialLogin $socialLogin )
    {
        $this->socialLogin = $socialLogin;
		//set scope
        $this->socialLogin->facebook->setScope( ['email'] );
        $this->socialLogin->google->setScope( array( "https://www.googleapis.com/auth/plus.me", "https://www.googleapis.com/auth/userinfo.email" ) );		
    }


    //$facebookLoginUrl = $this->socialLogin->facebook->getLoginUrl();
    //$googleLoginUrl = $this->socialLogin->google->getLoginUrl();
    //$twitterLoginUrl = $this->socialLogin->twitter->getLoginUrl();

    //dump( $this->socialLogin->getSocialLoginCookie() );

    //$this->template->facebookLastLogin = $this->socialLogin->facebook->isThisServiceLastLogin();
    //$this->template->googleLastLogin = $this->socialLogin->google->isThisServiceLastLogin();
    //$this->template->twitterLastLogin = $this->socialLogin->twitter->isThisServiceLastLogin();
    ...
```

```html
    <a rel="nofollow" href="{$facebookLoginUrl}" {if $facebookLastLogin}class="last-login"{/if}><i class="fa fa-facebook-square fa-lg"></i></a>
    <a rel="nofollow" href="{$googleLoginUrl}" {if $googleLastLogin}class="last-login"{/if}><i class="fa fa-google-plus-square fa-lg"></i></a><br/>
    <a rel="nofollow" href="{plink User:twitterLogin}" {if $twitterLastLogin}class="last-login"{/if}><i class="fa fa-twitter-square fa-lg"></i></a><br/>
    <a rel="nofollow" href="{plink User:registration}"><i class="fa fa-plus-square fa-lg"></i> Zaregistrovat</a>
```

```php
    public function actionTwitterLogin()
    {
        $this->redirectUrl( $this->socialLogin->twitter->getLoginUrl( $this->presenter->link( '//Homepage:googleLogin' ) ) );
    }
```

HomepagePresenter.php
```php
    public function actionFacebookLogin()
    {
        try
        {
            $me = $this->socialLogin->facebook->getMe( array( FacebookLogin::ID, FacebookLogin::EMAIL, FacebookLogin::NAME, FacebookLogin::FIRST_NAME, FacebookLogin::LAST_NAME ) );
            dump( $me );
            exit();
        }
        catch( Exception $e )
        {
            $this->flashMessage( $e->getMessage(), "alert-danger" );
            $this->redirect("Homepage:default");
        }
    }

    public function actionGoogleLogin( $code )
    {
        try
        {
            $me = $this->socialLogin->google->getMe( $code );
            dump( $me );
            exit();
        }
        catch( Exception $e )
        {
            $this->flashMessage( $e->getMessage(), "alert-danger" );
            $this->redirect("Homepage:default");
        }
    }
    ...
```

Registration
-------------

Facebook
-------------
[Facebook Developers](https://developers.facebook.com/) - create new website app. Full: Settings -> Web page -> Site URL : http://www.mypage.com

Google
-------------
[API Console - Google Code](https://console.developers.google.com) - create new project
add Google+ API: APIs & auth -> APIs -> Google+ API set ON
credentials: APIs & auth -> Credentials -> Crate new Client ID -> Web application

Twitter
-------------
[Register a new app at dev.twitter.com/apps/](https://apps.twitter.com/app/new)
