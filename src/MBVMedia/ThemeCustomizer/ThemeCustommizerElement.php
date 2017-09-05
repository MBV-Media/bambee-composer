<?php

/**
 * ThemeCustommizerElement.php
 *
 * @see https://github.com/MBV-Media/bambee-core
 */

namespace MBVMedia\ThemeCustomizer;


/**
 * Class ThemeCustommizerElement
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.5.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/ThemeCustomizer/ThemeCustommizerElement.html
 */
abstract class ThemeCustommizerElement {

    /**
     * @var
     *
     * @ignore
     */
    private $id;

    /**
     * @var array
     *
     * @ignore
     */
    private $args;

    /**
     * ThemeCustommizerElement constructor.
     *
     * @param $id
     * @param array $args
     *
     * @return void
     */
    public function __construct( $id, array $args ) {

        $this->id = $id;
        $this->args = $args;

    }

    /**
     * Get the id.
     *
     * @return mixed
     */
    public function getId() {

        return $this->id;

    }

    /**
     * Get the all arguments.
     *
     * @return array
     */
    public function getArgs() {

        return $this->args;

    }

    /**
     * Set argument.
     *
     * @param $name
     * @param $value
     *
     * @return void
     */
    public function setArg( $name, $value ) {

        $this->args[$name] = $value;

    }

    /**
     * Register the customizer element with the WP_Csutomize_Manager
     *
     * @param \WP_Customize_Manager $wpCustomize
     *
     * @return void
     */
    public abstract function register( \WP_Customize_Manager $wpCustomize );

}