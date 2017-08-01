<?php
/**
 * @since 1.0.0
 * @author hterhoeven
 * @licence MIT
 */

namespace MBVMedia\ThemeCustomizer;


class Setting extends ThemeCustommizerElement {

    /**
     * @var Control
     */
    private $control;

    /**
     * Setting constructor.
     * @param $id
     * @param array $settingArgs
     * @param array $controlArgs
     */
    public function __construct( $id, array $settingArgs, $controlArgs = array() ) {
        parent::__construct( $id, $settingArgs );
        $this->control = new Control( $id . '_control', $controlArgs );
    }

    /**
     * @return Control
     */
    public function getControl() {
        return $this->control;
    }

    /**
     * @param \WP_Customize_Manager $wpCustomize
     */
    public function register( \WP_Customize_Manager $wpCustomize ) {
        $wpCustomize->add_setting( $this->getId(), $this->getArgs() );
        $this->control->register( $wpCustomize );
    }
}