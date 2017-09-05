<?php
/**
 * Control.php
 */

namespace MBVMedia\ThemeCustomizer;


/**
 * Class Control
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.5.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/ThemeCustomizer/Control.html
 */
class Control extends ThemeCustommizerElement {

    /**
     * {@inheritdoc}
     */
    public function register( \WP_Customize_Manager $wpCustomize ) {

        $wpCustomize->add_control( $this->getId(), $this->getArgs() );

    }

}