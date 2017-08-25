<?php


/**
 * SessionControlledTemplate.php
 *
 * @see https://github.com/MBV-Media/bambee-core
 */

namespace MBVMedia\ControlledTemplate;


use MBVMedia\Lib\Session;
use MBVMedia\Lib\ThemeView;

/**
 * Class SessionControlledTemplate
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.5.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/ControlledTemplate/SessionControlledTemplate.html
 */
class SessionControlledTemplate extends ControlledTemplate {

    /**
     * @var string
     *
     * @ignore
     */
    private $sessionVar;

    /**
     * SessionControlledTemplate constructor.
     *
     * @param ThemeView $template
     * @param string $sessionVar
     * @param string $selectorOnClick
     * @param string $selectorContainer
     */
    public function __construct( ThemeView $template, $sessionVar, $selectorOnClick, $selectorContainer ) {

        $this->sessionVar = $sessionVar;
        parent::__construct( $template, $sessionVar, $selectorOnClick, $selectorContainer );

    }

    /**
     * {@inheritdoc}
     */
    public function addActions() {

        add_action( 'init', [ 'MBVMedia\Lib\Session', 'start' ] );
        parent::addActions();

    }

    /**
     * {@inheritdoc}
     */
    public function hide() {

        Session::setVar( $this->sessionVar, true );

    }

    /**
     * {@inheritdoc}
     */
    public function hidden() {

        return Session::getVar( $this->sessionVar ) === true;

    }

}