<?php

namespace Vencax;

use Nette;
use Vencax;

class SocialLogin extends BaseLogin
{

    /**
     * @var Params from config
     */
    private $params;

    /** @var Vencax\FacebookLogin */
    public $facebook;

    /** @var Vencax\GoogleLogin */
    public $google;

    /** @var Vencax\Twitter */
    public $twitter;

    public function __construct( $params, Nette\Http\Session $session )
    {
        $this->params = $params;
        if( count( $this->params["facebook"] ) > 0 )
        {
            $this->facebook = new FacebookLogin( $this->params["facebook"] );
        }

        if( count( $this->params["google"] ) > 0 )
        {
            $this->google = new GoogleLogin( $this->params["google"] );
        }

        if( count( $this->params["twitter"] ) > 0 )
        {
            $this->twitter = new TwitterLogin( $this->params["twitter"], $session );
        }
    }

}