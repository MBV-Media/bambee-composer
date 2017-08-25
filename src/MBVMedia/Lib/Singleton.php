<?php

/**
 * Singleton.php
 *
 * @see https://github.com/MBV-Media/bambee-core
 */

namespace MBVMedia\Lib;


/**
 * Interface Singleton
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.5.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/Lib/Singleton.html
 */
interface Singleton {

    /**
     * Get the instance itself.
     *
     * @return static
     *
     * @since 1.5.0
     */
    public static function self();

}