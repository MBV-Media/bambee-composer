<?php
/**
 * MetaKeyTaxonomy.php
 */

namespace MBVMedia\MetaBox;


use MBVMedia\Lib\ThemeView;

/**
 * Class MetaKeyTaxonomy
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.6.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/MetaBox/MetaKeyTaxonomy.html
 */
class MetaKeyTaxonomy extends MetaKey {

    /**
     * @var array
     *
     * @since 1.6.0
     * @ignore
     */
    private $taxonomy;

    /**
     * @var ThemeView
     *
     * @since 1.6.0
     * @ignore
     */
    private $termTemplate;

    /**
     * MetaKeyCheckbox constructor.
     *
     * @param $key
     * @param $label
     * @param int $type
     *
     * @since 1.6.0
     */
    public function __construct( $key, $label, $type = self::TYPE_DEFAULT ) {

        $this->taxonomy = [];

        $this->setTermTemplate( new ThemeView( 'partials/admin/meta-key-term-default.php' ) );

        $defaultTemplate = new ThemeView( 'partials/admin/meta-key-taxonomy-default.php' );
        $this->setTemplate( $defaultTemplate );

        parent::__construct( $key, $label, $type );

    }

    /**
     * Add a taxonomy.
     *
     * @param $taxonomy
     *
     * @return void
     *
     * @since 1.6.0
     */
    public function addTaxonomy( $taxonomy ) {

        $this->taxonomy[] = $taxonomy;

    }

    /**
     * Get the taxonomies.
     *
     * @return array
     *
     * @since 1.6.0
     */
    public function getTaxonomies() {

        return $this->taxonomy;

    }

    /**
     * Set the term template.
     *
     * @param ThemeView $template
     *
     * @return void
     *
     * @since 1.6.0
     */
    public function setTermTemplate( ThemeView $template ) {

        $template->setArg( 'metaKey', $this );
        $this->termTemplate = $template;

    }

    /**
     * {@inheritdoc}
     *
     * @param $postId
     *
     * @return array
     */
    public function getValue( $postId = null ) {

        $value = parent::getValue( $postId );
        return empty( $value ) ? [] : $value;

    }

}