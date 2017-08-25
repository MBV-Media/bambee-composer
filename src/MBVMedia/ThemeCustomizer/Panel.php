<?php

/**
 * Panel.php
 */

namespace MBVMedia\ThemeCustomizer;

/**
 * Class Panel
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.5.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/ThemeCustomizer/Panel.html
 */
class Panel extends ThemeCustommizerElement {

    /**
     * @var array
     *
     * @ignore
     */
    private $sectionList;

    /**
     * Panel constructor.
     *
     * @param $id
     * @param array $args
     */
    public function __construct( $id, array $args ) {

        parent::__construct( $id, $args );
        $this->sectionList = [];

    }

    /**
     * Adds a section.
     *
     * @param Section $section
     *
     * @return void
     */
    public function addSection( Section $section ) {

        $section->setArg( 'panel', $this->getId() );
        $this->sectionList[$section->getId()] = $section;

    }

    /**
     * Get a section.
     *
     * @param $id
     *
     * @return Section|null
     */
    public function getSection( $id ) {

        return isset( $this->sectionList[$id] ) ? $this->sectionList[$id] : null;

    }

    /**
     * {@inheritdoc}
     */
    public function register( \WP_Customize_Manager $wpCustomize ) {

        foreach ( $this->sectionList as $section ) {
            $section->register( $wpCustomize );
        }
        $wpCustomize->add_panel( $this->getId(), $this->getArgs() );

    }

}