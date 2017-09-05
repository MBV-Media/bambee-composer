<?php

/**
 * MetaKeyColorPicker.php
 */

namespace MBVMedia\MetaBox;


use MBVMedia\Lib\ThemeView;

/**
 * Class MetaKeyColorPicker
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.6.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/MetaBox/MetaKeyColorPicker.html
 */
class MetaKeyColorPicker extends MetaKey {

    /**
     * MetaKeyTextfield constructor.
     *
     * @param $key
     * @param $label
     * @param int $type
     */
    public function __construct( $key, $label, $type = self::TYPE_DEFAULT ) {

        $defaultTemplate = new ThemeView( 'partials/admin/meta-key-color-picker-default.php' );
        $this->setTemplate( $defaultTemplate );

        add_action( 'admin_enqueue_scripts', [ $this, 'actionAdminEnqueueScripts' ] );

        parent::__construct( $key, $label, $type );

    }

    /**
     * Admin enqueue scripts action callback
     *
     * @return void
     */
    public function actionAdminEnqueueScripts() {

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );

    }

}