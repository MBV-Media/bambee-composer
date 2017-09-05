<?php

/**
 * Singleton.php
 *
 * @see https://github.com/MBV-Media/bambee-core
 */

namespace MBVMedia\MetaBox;


use MBVMedia\Lib\ThemeView;

/**
 * Class MetaKeyCheckbox
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.5.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/MetaBox/MetaKeyCheckbox.html
 */
class MetaKeyCheckbox extends MetaKey {

    /**
     * {@inheritdoc}
     */
    public function __construct( $key, $label, $type = self::TYPE_DEFAULT ) {

        $defaultTemplate = new ThemeView( 'partials/admin/meta-key-checkbox-default.php' );
        $this->setTemplate( $defaultTemplate );

        parent::__construct( $key, $label, $type );

    }

}