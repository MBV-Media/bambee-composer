<?php
/**
 * @since 1.0.0
 * @author hterhoeven
 * @licence MIT
 */

namespace MBVMedia\Lib;


interface Singleton {

    /**
     * @return static
     */
    public static function self();

}