<?php

/**
 * Session.php
 *
 * @see https://github.com/MBV-Media/bambee-core
 */

namespace MBVMedia\Lib;


/**
 * Class Session
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.5.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/Lib/Session.html
 */
class Session {

    /**
     * Start the session.
     *
     * @param int $lifetime (optional) (default: 0)
     */
    public static function start( $lifetime = 0 ) {

        if ( session_status() == PHP_SESSION_NONE ) {
            session_set_cookie_params( $lifetime );
            session_start();
        }

    }

    /**
     * Get a session variable.
     *
     * @param $var
     *
     * @return mixed|null
     */
    public static function getVar( $var ) {

        if ( isset( $_SESSION[$var] ) ) {
            return $_SESSION[$var];
        }

        return null;

    }

    /**
     * Set a session variable.
     *
     * @param $var
     * @param $value
     *
     * @return void
     */
    public static function setVar( $var, $value ) {

        $_SESSION[$var] = $value;

    }

}