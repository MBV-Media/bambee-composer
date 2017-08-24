<?php

namespace MBVMedia\Shortcode;


use MBVMedia\BambeeWebsite;
use MBVMedia\Shortcode\Lib\BambeeShortcode;

/**
 * Load a core_data field.
 *
 * @package MBVMedia\lib\shortcode
 * @since 1.0.0
 * @param array $args
 * @param string $content
 * @return string
 *
 * @example
 *  Usage:
 *    [coredata]street[/coredata]
 */
class CoreData extends BambeeShortcode {

    /**
     * @inheritdoc
     */
    public function handleShortcode( array $atts = [], $content = '' ) {

        return nl2br( get_option( 'bambee_core_data_' . $content, '' ) );
    }
}