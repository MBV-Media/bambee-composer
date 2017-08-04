<?php

namespace MBVMedia\Shortcode\Lib;


interface Handleable {

    /**
     * @param array $atts
     * @param string $content
     * @return mixed
     */
    public function handleShortcode( array $atts = [], $content = '' );

}