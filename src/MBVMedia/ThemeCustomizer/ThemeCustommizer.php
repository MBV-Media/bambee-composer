<?php
/**
 * @since 1.0.0
 * @author hterhoeven
 * @licence MIT
 */

namespace MBVMedia\ThemeCustomizer;


class ThemeCustommizer {

    /**
     * @var array
     */
    private $elementList;

    /**
     * ThemeCustommizer constructor.
     */
    public function __construct() {

        $this->elementList = [];

    }

    /**
     * @param Panel $panel
     */
    public function addPanel( Panel $panel ) {

        $this->elementList[$panel->getId()] = $panel;

    }

    /**
     * @param Section $section
     */
    public function addSection( Section $section ) {

        $this->elementList[$section->getId()] = $section;

    }

    /**
     * @param $id
     * @return ThemeCustommizerElement|null
     */
    public function getElement( $id ) {

        return isset( $this->elementList[$id] ) ? $this->elementList[$id] : null;

    }

    /**
     *
     */
    public function register() {

        add_action( 'customize_register', [ $this, 'actionCustomizeRegister' ] );

    }

    /**
     * @param $wpCustomize
     */
    public function actionCustomizeRegister( $wpCustomize ) {

        foreach ( $this->elementList as $element ) {
            $element->register( $wpCustomize );
        }

    }

}