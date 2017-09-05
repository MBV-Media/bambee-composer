<?php

/**
 * PageLink.php
 *
 * @see https://github.com/MBV-Media/bambee-core
 */

namespace MBVMedia\Shortcode;


use MBVMedia\Shortcode\Lib\BambeeShortcode;

/**
 * Get permalink by id.
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.0.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/Shortcode/PageLink.html
 *
 * @example
 *  Usage:
 *    [page-link id=42]
 *
 */
class PageLink extends BambeeShortcode {

    /**
     * PageLink constructor.
     */
    public function __construct() {

        $this->addAttribute( 'id' );

    }

    /**
     * {@inheritdoc}
     */
    public function handleShortcode( array $atts = [], $content = '' ) {

        $id = $atts['id'];
        return get_permalink( $id );

    }

    /**
     * {@inheritdoc}
     */
    public static function getShortcodeAlias() {

        return 'page-link';

    }
}