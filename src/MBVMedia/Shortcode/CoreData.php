<?php

/**
 * CoreData.php
 */

namespace MBVMedia\Shortcode;


use MBVMedia\Shortcode\Lib\BambeeShortcode;

/**
 * Load a core_data field.
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.0.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/Shortcode/CoreData.html
 *
 * @example
 *  Usage:
 *    [coredata]street[/coredata]
 */
class CoreData extends BambeeShortcode {

    /**
     * {@inheritdoc}
     */
    public function handleShortcode( array $atts = [], $content = '' ) {

        return nl2br( get_option( 'bambee_core_data_' . $content, '' ) );
    }
}