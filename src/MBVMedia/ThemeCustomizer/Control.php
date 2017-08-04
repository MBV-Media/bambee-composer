<?php
/**
 * @since 1.0.0
 * @author hterhoeven
 * @licence MIT
 */

namespace MBVMedia\ThemeCustomizer;


class Control extends ThemeCustommizerElement {

    /**
     * @inheritdoc
     */
    public function register( \WP_Customize_Manager $wpCustomize ) {

        $wpCustomize->add_control( $this->getId(), $this->getArgs() );

    }

}