<?php
/**
 * @since 1.0.0
 * @author hterhoeven
 * @licence MIT
 */

namespace MBVMedia\ThemeCustomizer;


abstract class ThemeCustommizerElement {

    /**
     * @var
     */
    private $id;

    /**
     * @var array
     */
    private $args;

    /**
     * ThemeCustommizerElement constructor.
     * @param $id
     * @param array $args
     */
    public function __construct( $id, array $args ) {
        $this->id = $id;
        $this->args = $args;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getArgs() {
        return $this->args;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setArg( $name, $value ) {
        $this->args[$name] = $value;
    }

    /**
     * @param \WP_Customize_Manager $wpCustomize
     * @return mixed
     */
    public abstract function register( \WP_Customize_Manager $wpCustomize );
}