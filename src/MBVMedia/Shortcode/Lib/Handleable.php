<?php
/**
 * Handleable.php
 */

namespace MBVMedia\Shortcode\Lib;


/**
 * Interface Handleable
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.0.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/Shortcode/Lib/Handleable.html
 */
interface Handleable {

    /**
     * Implements the shortcode logic.
     *
     * @param array $atts (optional)
     * @param string $content (optional)
     *
     * @return mixed
     */
    public function handleShortcode( array $atts = [], $content = '' );

}