<?php
/**
 * Setting.php
 */

namespace MBVMedia\ThemeCustomizer;


/**
 * Class Setting
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.5.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/ThemeCustomizer/Setting.html
 */
class Setting extends ThemeCustommizerElement {

    /**
     * @var Control
     * @ignore
     */
    private $control;

    /**
     * Setting constructor.
     *
     * @param $id
     * @param array $settingArgs
     * @param array $controlArgs (optional)
     */
    public function __construct( $id, array $settingArgs, array $controlArgs = [] ) {

        parent::__construct( $id, $settingArgs );
        $this->control = new Control( $id . '_control', $controlArgs );
        $this->getControl()->setArg( 'settings', $this->getId() );

    }

    /**
     * Get the associated control.
     *
     * @return Control
     */
    public function getControl() {

        return $this->control;

    }

    /**
     * {@inheritdoc}
     */
    public function register( \WP_Customize_Manager $wpCustomize ) {

        $wpCustomize->add_setting( $this->getId(), $this->getArgs() );
        $this->control->register( $wpCustomize );

    }

}