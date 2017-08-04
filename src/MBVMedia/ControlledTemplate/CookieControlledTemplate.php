<?php
/**
 * @since 1.0.0
 * @author hterhoeven
 * @licence MIT
 */

namespace MBVMedia\ControlledTemplate;


use MBVMedia\Lib\Cookie;

class CookieControlledTemplate extends ControlledTemplate {

    /**
     * @var string
     */
    private $cookieName;

    /**
     * @var int
     */
    private $interval;

    /**
     * CookieControlledTemplate constructor.
     * @param \MBVMedia\Lib\ThemeView|string $template
     * @param string $cookieName
     * @param string $selectorOnClick
     * @param string $selectorContainer
     * @param int $interval
     */
    public function __construct( $template, $cookieName, $selectorOnClick, $selectorContainer, $interval = 86400 ) {

        $this->cookieName = $cookieName;
        $this->interval = $interval;
        parent::__construct( $template, $cookieName, $selectorOnClick, $selectorContainer );

    }

    /**
     *
     */
    public function hide() {

        Cookie::write( $this->cookieName, true, $this->interval );

    }

    /**
     * @return bool
     */
    public function hidden() {

        return Cookie::read( $this->cookieName ) == true;

    }

}