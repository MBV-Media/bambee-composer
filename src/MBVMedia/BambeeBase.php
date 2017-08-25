<?php

/**
 * BambeeBase.php
 *
 * @see https://github.com/MBV-Media/bambee-core
 */

namespace MBVMedia;


use MBVMedia\Lib\Singleton;

/**
 * Class BambeeBase
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.4.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/BambeeBase.html
 */
abstract class BambeeBase implements Singleton {

    /**
     * This is the place where Wordpress actions should be added.
     *
     * @return void
     */
    abstract public function addActions();

    /**
     * This is the place where Wordpress filters should be added.
     *
     * @return void
     */
    abstract public function addFilters();

}