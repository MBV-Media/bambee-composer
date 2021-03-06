<?php

/**
 * Section.php
 */

namespace MBVMedia\ThemeCustomizer;


/**
 * Class Section
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.5.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/ThemeCustomizer/Section.html
 */
class Section extends ThemeCustommizerElement {

    /**
     * @var array
     *
     * @ignore
     */
    private $settingList;

    /**
     * Section constructor.
     *
     * @param $id
     * @param array $args
     */
    public function __construct( $id, array $args ) {

        parent::__construct( $id, $args );
        $this->settingList = [];

    }

    /**
     * Add a setting.
     *
     * @param Setting $setting
     *
     * @return void
     */
    public function addSetting( Setting $setting ) {

        $setting->getControl()->setArg( 'section', $this->getId() );
        $this->settingList[$setting->getId()] = $setting;

    }

    /**
     * {@inheritdoc}
     */
    public function register( \WP_Customize_Manager $wpCustomize ) {

        $wpCustomize->add_section( $this->getId(), $this->getArgs() );

        foreach ( $this->settingList as $setting ) {
            $setting->register( $wpCustomize );
        }

    }

}