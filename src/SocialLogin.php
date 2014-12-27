<?php

namespace Vencax;

use Nette;
use Vencax;

class SocialLogin extends BaseLogin
{

    /** @var Params from config */
    private $params;

    /** @var Vencax\FacebookLogin */
    public $facebook;

    /** @var Vencax\GoogleLogin */
    public $google;

    /** @var Vencax\Twitter */
    public $twitter;

    /**
     * @param $params params from cnofig.neon
     * @param Nette\Http\Response $httpResponse
     * @param Nette\Http\Request $httpRequest
     * @param Nette\Http\Session $session
     */
    public function __construct( $params, Nette\Http\Response $httpResponse, Nette\Http\Request $httpRequest, Nette\Http\Session $session )
    {
        $this->params = $params;
        $this->httpResponse = $httpResponse;
        $this->httpRequest = $httpRequest;

        if ( count( $this->params["facebook"] ) > 0 ) {
            $this->facebook = new FacebookLogin( $this->params["facebook"], $this->httpResponse, $this->httpRequest );
        }

        if ( count( $this->params["google"] ) > 0 ) {
            $this->google = new GoogleLogin( $this->params["google"], $this->httpResponse, $this->httpRequest );
        }

        if ( count( $this->params["twitter"] ) > 0 ) {
            $this->twitter = new TwitterLogin( $this->params["twitter"], $session, $this->httpResponse, $this->httpRequest );
        }
    }

}