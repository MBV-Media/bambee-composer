<?php

/**
 * ThemeView.php
 */

namespace MBVMedia\Lib;

use MBVMedia\Bambee;


/**
 * The class representing a view.
 *
 * @package BambeeCore
 * @author R4c00n <marcel.kempf93@gmail.com>
 * @licence MIT
 * @since 1.0.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/Lib/ThemeView.html
 *
 * @example
 *  Usage:
 *    $template = new ThemeView( 'path/to/view.php', [
 *      'param1' => 'This is passed to the view as $param1'
 *    ] );
 *    echo $template->render()
 */
class ThemeView {

    /**
     * The arguments passed to the view.
     *
     * @var array
     *
     * @since 1.0.0
     */
    protected $args;

    /**
     * Filename of the view.
     *
     * @var string
     *
     * @since 1.0.0
     */
    protected $file;

    /**
     * ThemeView constructor.
     *
     * @param string $file
     * @param array $args (optional)
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function __construct( $file, $args = [] ) {

        $this->file = $file;
        $this->args = $args;

    }

    /**
     * Set an argument.
     *
     * @param $arg
     * @param $value
     *
     * @return void
     */
    public function setArg( $arg, $value ) {

        $this->args[$arg] = $value;

    }

    /**
     * Render the view.
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function render() {

        extract( $this->args );
        ob_start();
        if ( locate_template( $this->file ) ) {
            require( locate_template( $this->file ) );
        }
        $templatePart = ob_get_clean();

        return $templatePart;

    }

    /**
     * Renders and echos the view.
     *
     * @return void
     *
     * @since 1.7.0
     */
    public function renderAndEcho() {

        echo $this->render();

    }

}