<?php

namespace MBVMedia\Shortcode;


use MBVMedia\Shortcode\Lib\BambeeShortcode;

/**
 * Generate a foundation grid row.
 *
 * @package MBVMedia\lib\shortcode
 * @since 1.0.0
 * @param array $args
 * @param string $content
 * @return string
 *
 * @example
 *  Usage:
 *    [row]Hello World![/row]
 */
class Row extends BambeeShortcode {

    /**
     * Row constructor.
     */
    public function __construct() {

        $this->addAttribute( 'class' );

    }

    /**
     * @inheritdoc
     */
    public function handleShortcode( array $atts = [], $content = '' ) {

        $class = isset( $atts['class'] ) ? $atts['class'] : '';
        $content = sprintf(
            '<div class="row %s">%s</div>',
            $class,
            $content
        );
        return $content;

    }
}