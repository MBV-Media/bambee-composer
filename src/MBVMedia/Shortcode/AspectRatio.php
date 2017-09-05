<?php

/**
 * AspectRatio.php
 *
 * @see https://github.com/MBV-Media/bambee-core
 */

namespace MBVMedia\Shortcode;


use MBVMedia\Shortcode\Lib\BambeeShortcode;

/**
 * Class AspectRatio
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.5.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/Shortcode/AspectRatio.html
 */
class AspectRatio extends BambeeShortcode {

    /**
     * @var array
     *
     * @ignore
     */
    private $predefinedRatioList;

    /**
     * @var string
     *
     * @ignore
     */
    private $ratio;

    /**
     * AspectRatio constructor.
     */
    public function __construct() {

        $this->addAttribute( 'ratio', '16:9' );
        $this->addAttribute( 'class' );

        $this->predefinedRatioList = [
            'square' => 'square',
            '1:1' => 'square',
            '2:1' => '2to1',
            '16:9' => '16to9',
            '4:3' => '4to3',
            '1:2' => '1to2',
            '3:4' => '3to4',
            '9:16' => '9to16',
        ];

        $this->ratio = '';

    }

    /**
     * {@inheritdoc}
     */
    public function handleShortcode( array $atts = [], $content = '' ) {

        $this->ratio = $atts['ratio'];

        $ratioClass = $this->getRatioClass();

        try {
            $ratioStyle = empty( $ratioClass ) ? $this->getRatioStyle() : '';
        } catch ( \InvalidArgumentException $e ) {
            return $e->getMessage();
        }

        $class = empty( $atts['class'] ) ? '' : ' ' . $atts['class'];

        $output = '<div class="responsive-aspect-ratio%s"%s>';
        $output .= '    <div class="aspect-ratio-content%s">';
        $output .= '        ' . $content;
        $output .= '    </div>';
        $output .= '</div>';

        $output = sprintf( $output, $ratioClass, $ratioStyle, $class );

        return $output;

    }

    /**
     * {@inheritdoc}
     */
    public static function getShortcodeAlias() {

        return 'aspect-ratio';

    }


    /**
     * Get the predefined ratio list.
     *
     * @return array
     */
    public function &getPredefinedRatioList() {

        return $this->predefinedRatioList;

    }

    /**
     * @throws \InvalidArgumentException
     * @return string
     *
     * @ignore
     */
    private function getRatioClass() {

        return isset( $this->predefinedRatioList[$this->ratio] )
            ? ' ratio-' . $this->predefinedRatioList[$this->ratio]
            : '';

    }

    /**
     * @return string
     *
     * @ignore
     */
    private function getRatioStyle() {

        return ' style="padding-top: ' . $this->calculatePadding() . '%;"';

    }

    /**
     * @throws \InvalidArgumentException
     * @return float
     *
     * @ignore
     */
    private function calculatePadding() {

        $ratio = explode( ':', $this->ratio );

        if ( count( $ratio ) !== 2 ) {
            throw new \InvalidArgumentException( __( 'Not a valid ratio.', TextDomain ) );
        }

        return $ratio[1] / $ratio[0] * 100;

    }

}