<?php
/**
 * @since 1.0.0
 * @author hterhoeven
 * @licence MIT
 */

namespace MBVMedia\ThemeCustomizer;


class Panel extends ThemeCustommizerElement {

    /**
     * @var array
     */
    private $sectionList;

    /**
     * Panel constructor.
     * @param $id
     * @param array $args
     */
    public function __construct( $id, array $args ) {
        parent::__construct( $id, $args );
        $this->sectionList = array();
    }

    /**
     * @param Section $section
     */
    public function addSection( Section $section ) {
        $section->setArg( 'panel', $this->getId() );
        $this->sectionList[$section->getId()] = $section;
    }

    /**
     * @param $id
     * @return Section|null
     */
    public function getSection( $id ) {
        return isset( $this->sectionList[$id] ) ? $this->sectionList[$id] : null;
    }

    /**
     * @param \WP_Customize_Manager $wpCustomize
     */
    public function register( \WP_Customize_Manager $wpCustomize ) {

        foreach( $this->sectionList as $section ) {
            $section->register( $wpCustomize );
        }
        $wpCustomize->add_panel( $this->getId(), $this->getArgs() );
    }
}