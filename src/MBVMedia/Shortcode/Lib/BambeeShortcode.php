<?php

/**
 * BambeeShortcode.php
 */

namespace MBVMedia\Shortcode\Lib;


/**
 * Class BambeeShortcode
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.0.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/Shortcode/Lib/BambeeShortcode.html
 */
abstract class BambeeShortcode implements Handleable {

    /**
     * The supported attributes.
     *
     * @var array
     * @ignore
     */
    private $supportedAtts;

    /**
     * A description of the shortcode.
     *
     * @var string
     * @ignore
     */
    private $description;

    /**
     * BambeeShortcode constructor.
     */
    public function __construct() {
        $this->supportedAtts = [];
    }

    /**
     * Get all supported attributes.
     *
     * @return array An array of attributes.
     */
    public function getSupportedAtts() {
        return $this->supportedAtts;
    }

    /**
     * Add an attribute.
     *
     * @param $name
     * @param string $default (optional)
     * @param string $type (optional) TinyMCE input type
     *
     * @return void
     */
    public function addAttribute( $name, $default = '', $type = 'text' ) {

//        $this->supportedAtts[$name] = $default;
        $this->supportedAtts[] = [
            'name' => $name,
            'default' => $default,
            'type' => $type,
        ];

    }

    /**
     * Get the description.
     *
     * @return mixed
     */
    public function getDescription() {

        return $this->description;

    }

    /**
     * Set the description.
     *
     * @param mixed $description
     *
     * @return void
     */
    public function setDescription( $description ) {

        $this->description = $description;

    }

    /**
     * Adds the shortcode to Wordpress.
     *
     * @return void
     */
    public static function addShortcode() {

        $tag = static::getShortcodeAlias();

        $class = get_called_class();
        if ( empty( $tag ) ) {
            $tag = self::getUnqualifiedClassName( $class );
        }

        add_shortcode( $tag, [ $class, 'doShortcode' ] );

    }

    /**
     * Executes the shortcode.
     *
     * @param array $atts (optional)
     * @param string $content (optional)
     *
     * @return mixed
     */
    public static function doShortcode( $atts = [], $content = '' ) {

        $shortcodeObject = new static();
        $supportedAtts = $shortcodeObject->getSupportedAtts();
        $defaultAtts = [];
        foreach ( $supportedAtts as $attribute ) {
            $defaultAtts[$attribute['name']] = $attribute['default'];
        }

        /* TODO: Add shortcode name as argument to shortcode_atts */
        $atts = shortcode_atts( $defaultAtts, $atts );

        return do_shortcode( $shortcodeObject->handleShortcode( $atts, $content ) );

    }

    /**
     * Get the alias for the shortcode.
     *
     * @return string
     */
    public static function getShortcodeAlias() {

        return self::getUnqualifiedClassName();

    }

    /**
     * Get the unqualified class name.
     *
     * @param string|object|null $class (optional)
     *
     * @return string
     */
    public static function getUnqualifiedClassName( $class = null ) {

        if ( $class === null ) {
            $class = get_called_class();
        }
        $reflect = new \ReflectionClass( $class );
        return strtolower( $reflect->getShortName() );

    }

}