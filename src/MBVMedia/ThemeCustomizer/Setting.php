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
    public function __construct( $id, array $settingArgs, $controlArgs = [] ) {

        parent::__construct( $id, $settingArgs );
        $this->control = new Control( $id . '_control', $controlArgs );
        $this->getControl()->setArg( 'settings', $this->getId() );

    }

    /**
     * @return Control
     */
    public function getControl() {

        return $this->control;

    }

    /**
     * @inheritdoc
     */
    public function register( \WP_Customize_Manager $wpCustomize ) {

        $wpCustomize->add_setting( $this->getId(), $this->getArgs() );
        $this->control->register( $wpCustomize );

    }

}