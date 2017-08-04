<?php

namespace MBVMedia\Shortcode;


use MBVMedia\Shortcode\Lib\BambeeShortcode;

/**
 * Get permalink by id.
 *
 * @package MBVMedia\lib\shortcode
 * @since 1.0.0
 *
 * @param array $args
 * @return mixed
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
     * @inheritdoc
     */
    public function handleShortcode( array $atts = array(), $content = '' ) {

        $id = $atts['id'];
        return get_permalink( $id );

    }

    /**
     * @inheritdoc
     */
    public static function getShortcodeAlias() {

        return 'page-link';

    }
}