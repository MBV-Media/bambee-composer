<?php
/**
 * CookieControlledTemplate.php
 */

namespace MBVMedia\ControlledTemplate;


use MBVMedia\Lib\Cookie;

/**
 * Class CookieControlledTemplate
 *
 * @package BambeeCore
 * @author hterhoeven
 * @licence MIT
 * @since 1.5.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/ControlledTemplate/CookieControlledTemplate.html
 */
class CookieControlledTemplate extends ControlledTemplate {

    /**
     * @var string
     *
     * @ignore
     */
    private $cookieName;

    /**
     * @var int
     *
     * @ignore
     */
    private $interval;

    /**
     * CookieControlledTemplate constructor.
     *
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
     * {@inheritdoc}
     */
    public function hide() {

        Cookie::write( $this->cookieName, true, $this->interval );

    }

    /**
     * {@inheritdoc}
     */
    public function hidden() {

        return Cookie::read( $this->cookieName ) == true;

    }

}