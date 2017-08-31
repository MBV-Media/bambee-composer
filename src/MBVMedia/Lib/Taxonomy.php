<?php

/**
 * Taxonomy.php
 */

namespace MBVMedia\Lib;


/**
 * Class Taxonomy
 *
 * @package BambeeCore
 * @author @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.7.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/Lib/Taxonomy.html
 */
class Taxonomy {

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $args;

    /**
     * @var array
     */
    private $postTypeList;

    /**
     * Taxonomy constructor.
     *
     * @param $name
     * @param array $args (optional) see <a href="https://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments" target="_blank">register_taxonomy arguments</a>
     *
     * @since 1.7.0
     */
    public function __construct( $name, array $args = [] ) {

        $this->name = $name;
        $this->args = $args;
        $this->postTypeList = [];

    }

    /**
     * Get the taxonomy name (slug).
     *
     * @return string
     *
     * @since 1.7.0
     */
    public function getName() {

        return $this->name;

    }

    /**
     * Add support for a specific post type.
     *
     * Note: By default the taxonomy will be added to every post type.
     * If you add supported post types, the taxonomy will only appear on these post types.
     *
     * @param $postType
     *
     * @return void
     *
     * @since 1.7.0
     */
    public function addPostTypeSupport( $postType ) {

        $this->postTypeList[] = $postType;

    }

    /**
     * Register the taxonomy with Wordpress.
     *
     * @return void
     *
     * @since 1.7.0
     */
    public function register() {

        if( empty( $this->postTypeList ) ) {
            $this->postTypeList = get_post_types();
        }

        register_taxonomy( $this->name, $this->postTypeList, $this->args );

        if( is_admin() ) {
            add_action( 'restrict_manage_posts', [$this, 'buildFilters'] );
        }

    }

    /**
     * Add a dropdown filter bar on the posts overview page.
     * Note: This method is called by the "restrict_manage_posts"-action and should not be called directly.
     *
     * @return void
     *
     * @since 1.7.0
     */
    public function buildFilters() {

        global $typenow;

        if( in_array( $typenow, $this->postTypeList ) ) {

            $taxonomy = get_taxonomy( $this->name );

            wp_dropdown_categories( [
                'show_option_all' => sprintf( __('Show all %s', TextDomain ), $taxonomy->label ),
                'taxonomy' => $taxonomy->name,
                'name' => $taxonomy->name,
                'orderby' => 'term_order',
                'selected' => filter_input( INPUT_GET, $taxonomy->query_var ),
                'hierarchical' => $taxonomy->hierarchical,
                'show_count' => true,
                'hide_empty' => false,
                'value_field' => 'slug',
            ] );
        }

    }

}