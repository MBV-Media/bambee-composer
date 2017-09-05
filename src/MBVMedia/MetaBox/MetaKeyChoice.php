<?php
/**
 * MetaKeyChoice.php
 */

namespace MBVMedia\MetaBox;


use MBVMedia\Lib\ThemeView;

/**
 * Class MetaKeyChoice
 *
 * @package BambeeCore
 * @author hterhoeven
 * @licence MIT
 * @since 1.6.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/MetaBox/MetaKeyChoice.html
 */
class MetaKeyChoice extends MetaKey {

    /**
     * @var array
     *
     * @ignore
     */
    private $choices;

    /**
     * MetaKeyTextfield constructor.
     *
     * @param $key
     * @param $label
     * @param int $type
     */
    public function __construct( $key, $label, $type = self::TYPE_DEFAULT ) {

        $this->choices = [];

        $defaultTemplate = new ThemeView( 'partials/admin/meta-key-choice-default.php' );
        $this->setTemplate( $defaultTemplate );

        parent::__construct( $key, $label, $type );

    }

    /**
     * Get the choices.
     *
     * @return array
     */
    public function getChoices() {

        return $this->choices;

    }

    /**
     * Add a choice.
     *
     * @param $value
     * @param $label
     *
     * @return void
     */
    public function addChoice( $value, $label ) {

        $this->choices[] = [
            'value' => $value,
            'label' => $label,
        ];

    }

}