<?php

/**
 * Cookie.php
 *
 * @see https://github.com/MBV-Media/bambee-core
 */

namespace MBVMedia\Lib;


/**
 * Class Cookie
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.5.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/Lib/Cookie.html
 */
class Cookie {

    /**
     * Read a cookie.
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function read( $name ) {

        /*
         * Dont't use filter_input in this place.
         * It will always be null if the cookie was set in this process.
         */
        return isset( $_COOKIE[$name] ) ? $_COOKIE[$name] : null;

    }

    /**
     * Write a cookie.
     *
     * @param string $name
     * @param mixed $value
     * @param int $expire
     * @param string $path
     *
     * @return void
     */
    public static function write( $name, $value, $expire = 86400, $path = '/' ) {

        $_COOKIE[$name] = $value;
        setcookie( $name, $value, time() + $expire, $path );

    }

}