social-login
===============

[![Build Status](https://travis-ci.org/venca-x/social-login.svg?branch=master)](https://travis-ci.org/github/venca-x/social-login)
[![Coverage Status](https://coveralls.io/repos/github/venca-x/social-login/badge.svg?branch=master)](https://coveralls.io/github/venca-x/social-login?branch=master) 
[![Latest Stable Version](https://poser.pugx.org/venca-x/social-login/v/stable.svg)](https://packagist.org/packages/venca-x/social-login) 
[![Latest Unstable Version](https://poser.pugx.org/venca-x/social-login/v/unstable.svg)](https://packagist.org/packages/venca-x/social-login) 
[![Total Downloads](https://poser.pugx.org/venca-x/social-login/downloads.svg)](https://packagist.org/packages/venca-x/social-login) 
[![License](https://poser.pugx.org/venca-x/social-login/license.svg)](https://packagist.org/packages/venca-x/social-login)

Nette addon for login with social networks

| Version     | Facebook App API | PHP     | Recommended Nette             |
| ---         | ---              | ---     | ---                           |
| dev-master  | 8.0 or own       | \>= 7.2 (support 8.0) | Nette 3.0                     |
| 1.2.x       | 8.0 or own       | \>= 7.2 (support 8.0) | Nette 3.0                     |
| 1.1.x       | 2.6              | \>= 7.0 | Nette 2.4 (Nette\SmartObject) |
| 1.0.x       | 2.6              | \>= 5.5 | Nette 2.4, 2.3 (Nette\Object) |

[All permissions for Facebook fields](https://developers.facebook.com/docs/graph-api/reference/user/)

Installation
------------

Install **dev-master** version for **Nette 3.0**:
```
composer require venca-x/social-login:dev-master
```

Install **1.2.x** version for **Nette 3.0** (Nette\SmartObject):
```
composer require venca-x/social-login:^1.2.0
```


Install **1.1.x** version for **Nette 2.4** (Nette\SmartObject):
```
composer require venca-x/social-login:^1.1.0
```

Install **1.0.x** version for **Nette 2.4** or **Nette 2.3** (Nette\Object):
```
composer require venca-x/social-login:^1.0.0
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
			defaultFbGraphVersion: 'v8.0'
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
        - VencaX\SocialLogin({ facebook: %facebook%, google: %google%, twitter: %twitter% }, 'domain-social-login' )
```
Where 'domain-social-login' replace to your unique identifier (it's cookie name for last used services for login)


BasePresenter.php

```php
    use VencaX;

    /** @var VencaX\SocialLogin */
    private $socialLogin;

    public function injectSocialLogin( VencaX\SocialLogin $socialLogin )
    {
        $this->socialLogin = $socialLogin;
        
        //set scope
        $this->socialLogin->facebook->setScope( ['email'] );
        $this->socialLogin->google->setScope( array( "https://www.googleapis.com/auth/plus.me", "https://www.googleapis.com/auth/userinfo.email" ) );		
    }

    public function renderIn() {
        //$facebookLoginUrl = $this->socialLogin->facebook->getLoginUrl();
        //$googleLoginUrl = $this->socialLogin->google->getLoginUrl();
        //$twitterLoginUrl = $this->socialLogin->twitter->getLoginUrl();
        
        //dump( $this->socialLogin->getSocialLoginCookie() );
        
        //$this->template->facebookLastLogin = $this->socialLogin->facebook->isThisServiceLastLogin();
        //$this->template->googleLastLogin = $this->socialLogin->google->isThisServiceLastLogin();
        //$this->template->twitterLastLogin = $this->socialLogin->twitter->isThisServiceLastLogin();
        //...
    }
```

Layout for in.latte:
```html
    <a rel="nofollow" href="{$facebookLoginUrl}" {if $facebookLastLogin}class="last-login"{/if}><i class="fa fa-facebook-square fa-lg"></i></a>
    <a rel="nofollow" href="{$googleLoginUrl}" {if $googleLastLogin}class="last-login"{/if}><i class="fa fa-google-plus-square fa-lg"></i></a><br/>
    <a rel="nofollow" href="{plink User:twitterLogin}" {if $twitterLastLogin}class="last-login"{/if}><i class="fa fa-twitter-square fa-lg"></i></a><br/>
    <a rel="nofollow" href="{plink User:registration}"><i class="fa fa-plus-square fa-lg"></i> Zaregistrovat</a>
```

### Simple login ###
HomepagePresenter.php
```php
    public function actionFacebookLogin()
    {
        try
        {
            $me = $this->socialLogin->facebook->getMe( array( FacebookLogin::ID, FacebookLogin::EMAIL, FacebookLogin::NAME, FacebookLogin::FIRST_NAME, FacebookLogin::LAST_NAME ) );
            dump( $me );
            exit;
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
            exit;
        }
        catch( Exception $e )
        {
            $this->flashMessage( $e->getMessage(), "alert-danger" );
            $this->redirect("Homepage:default");
        }
    }
    //...
```
### Simple logint with Twitter ###

```php
    public function actionTwitterLogin($oauth_token, $oauth_verifier)
    {
        try {
            $me = $this->socialLogin->twitter->getMe($oauth_token, $oauth_verifier);
            //$me = $this->socialLogin->twitter->getMe($oauth_token, $oauth_verifier, true);//when zou want user's email
            dump($me);
            exit;
        } catch (Exception $e) {
            $this->flashMessage($e->getMessage(), 'alert-danger');
            $this->redirect('Homepage:default');
        }
    }
```

### Login with backlink ###
Use it when you want to redirect to specific URL after success login

HomepagePresenter.php
```php
    private $backlink = null;
    
    //render where are links to social networks  
    public function renderIn() {
        if ($this->backlink) {
            $this->socialLogin->facebook->setState($this->backlink);
            $this->socialLogin->google->setState($this->backlink);
        }

        //$facebookLoginUrl = $this->socialLogin->facebook->getLoginUrl();
        //$googleLoginUrl = $this->socialLogin->google->getLoginUrl();
        //$twitterLoginUrl = $this->socialLogin->twitter->getLoginUrl();
    
        //dump( $this->socialLogin->getSocialLoginCookie() );
    
        //$this->template->facebookLastLogin = $this->socialLogin->facebook->isThisServiceLastLogin();
        //$this->template->googleLastLogin = $this->socialLogin->google->isThisServiceLastLogin();
        //$this->template->twitterLastLogin = $this->socialLogin->twitter->isThisServiceLastLogin();
    }

    public function actionFacebookLogin($state = NULL)
    {
        try
        {
            if ($state) $this->backlink = $state;
            $me = $this->socialLogin->facebook->getMe();
            //dump( $me );
            //exit();
            if($this->backlink != null) {
                $this->redirect($this->backlink);
            }
        }
        catch( Exception $e )
        {
            $this->flashMessage( $e->getMessage(), "alert-danger" );
            $this->redirect("Homepage:default");
        }
    }

    public function actionGoogleLogin( $code, $state = NULL )
    {
        try
        {
            if ($state) $this->backlink = $state;
            $me = $this->socialLogin->google->getMe( $code );
            //dump( $me );
            //exit();
            if($this->backlink != null) {
                $this->redirect($this->backlink);
            }
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
