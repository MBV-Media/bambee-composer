<?php

/**
 * MetaKeyTextarea.php
 *
 * @see https://github.com/MBV-Media/bambee-core
 */

namespace MBVMedia\MetaBox;


use MBVMedia\Lib\ThemeView;

/**
 * Class MetaKeyTextarea
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.6.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/MetaBox/MetaKeyTextarea.html
 */
class MetaKeyTextarea extends MetaKey {

    /**
     * MetaKeyTextfield constructor.
     *
     * @param $key
     * @param $label
     * @param int $type (optional)
     */
    public function __construct( $key, $label, $type = self::TYPE_DEFAULT ) {

        $defaultTemplate = new ThemeView( 'partials/admin/meta-key-textarea-default.php' );
        $this->setTemplate( $defaultTemplate );

        parent::__construct( $key, $label, $type );

    }

}