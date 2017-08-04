<?php
/**
 * @since 1.0.0
 * @author hterhoeven
 * @licence MIT
 */

namespace MBVMedia\ThemeCustomizer;


class Section extends ThemeCustommizerElement {

    /**
     * @var array
     */
    private $settingList;

    /**
     * Section constructor.
     * @param $id
     * @param array $args
     */
    public function __construct( $id, array $args ) {

        parent::__construct( $id, $args );
        $this->settingList = array();

    }

    /**
     * @param Setting $setting
     */
    public function addSetting( Setting $setting ) {

        $setting->getControl()->setArg( 'section', $this->getId() );
        $this->settingList[$setting->getId()] = $setting;

    }

    /**
     * @inheritdoc
     */
    public function register( \WP_Customize_Manager $wpCustomize ) {

        $wpCustomize->add_section( $this->getId(), $this->getArgs() );

        foreach ( $this->settingList as $setting ) {
            $setting->register( $wpCustomize );
        }

    }

}