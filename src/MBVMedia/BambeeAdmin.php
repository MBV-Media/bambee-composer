<?php

/**
 * BambeeAdmin.php
 */

namespace MBVMedia;


use MBVMedia\Lib\ThemeView;


/**
 * The class representing the WordPress Admin.
 *
 * @package BambeeCore
 * @author R4c00n <marcel.kempf93@gmail.com>
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.0.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/BambeeAdmin.html
 */
abstract class BambeeAdmin extends BambeeBase {

    /**
     * @var BambeeAdmin
     *
     * @since 1.5.0
     * @ignore
     */
    private static $instance = null;

    /**
     * BambeeAdmin constructor.
     */
    protected function __construct() {
    }

    /**
     * {@inheritdoc}
     */
    public function addActions() {

        add_action( 'after_switch_theme', [$this, 'addCapabilities'] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueueStyles' ] );
        add_action( 'admin_init', [ $this, 'registerMediaSettings' ] );
        add_action( 'admin_init', [ $this, 'displaySvgThumbs' ] );
        add_action( 'manage_posts_custom_column', [ $this, 'customColumnsData' ], 10, 2 );
        add_action( 'manage_pages_custom_column', [ $this, 'customColumnsData' ], 10, 2 );

    }

    /**
     * {@inheritdoc}
     */
    public function addFilters() {

        add_filter( 'upload_mimes', [ $this, 'addSvgMediaSupport' ] );
        add_filter( 'manage_posts_columns', [ $this, 'customColumns' ] );
        add_filter( 'manage_pages_columns', [ $this, 'customColumns' ] );

    }

    /**
     * Add capabilities to user roles.
     *
     * @return void
     *
     * @since 1.7.0
     */
    public function addCapabilities() {

        $role = get_role( 'administrator' );
        $role->add_cap( 'debug' );

    }

    /**
     * Enqueue the CSS.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function enqueueStyles() {

        wp_enqueue_style( 'custom_css', ThemeUrl . '/css/admin.min.css' );

    }

    /**
     * Register settings to the media screen.
     *
     * @return void
     *
     * @since 1.7.0
     */
    public function registerMediaSettings() {

        register_setting(
            'media',
            'bambee_featured_images',
            [ $this, 'validateFeaturedImagesOptions' ]
        );

        $settings = get_option( 'bambee_featured_images', Bambee::self()->getFeaturedImageDefaults() );
        $settingFeaturedImagesTemplate = new ThemeView( 'partials/admin/setting-featured-images.php', $settings );
        $settingFeaturedImagesCallback = [ $settingFeaturedImagesTemplate, 'renderAndEcho' ];
        add_settings_field(
            'featured_images_size',
            __( 'Featured Images' ),
            $settingFeaturedImagesCallback,
            'media',
            'default' // image sizes
        );

    }

    /**
     * Validates the featured image options.
     *
     * @param $input
     *
     * @return array
     */
    public function validateFeaturedImagesOptions( $input ) {

        $valid = [
            'width' => absint( $input['width'] ),
            'height' => absint( $input['height'] ),
            'crop' => boolval( $input['crop'] ),
        ];
        return $valid;

    }


    /**
     * Filter-hook callbacks
     */

    /**
     * Add SVG support to mimes.
     *
     * @param $mimes
     *
     * @return mixed
     */
    public function addSvgMediaSupport( $mimes ) {

        $mimes['svg'] = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';
        return $mimes;

    }

    /**
     * Show SVG thumbnails.
     *
     * @return void
     */
    public function displaySvgThumbs() {

        ob_start();

        add_action( 'shutdown', [ $this, 'svgThumbsFilter' ], 0 );
        add_filter( 'final_output', [ $this, 'svgFinalOutput' ] );

    }

    /**
     * Filter SVG thumbnails.
     *
     * @return void
     */
    public function svgThumbsFilter() {

        $final = '';
        $obLevels = count( ob_get_level() );

        for ( $i = 0; $i < $obLevels; $i++ ) {

            $final .= ob_get_clean();

        }

        echo apply_filters( 'final_output', $final );

    }

    /**
     * Get the final SVG output.
     *
     * @param $content
     *
     * @return mixed
     */
    public function svgFinalOutput( $content ) {

        $content = str_replace(
            '<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
            '<# } else if ( \'svg+xml\' === data.subtype ) { #>
				<img class="details-image" src="{{ data.url }}" draggable="false" />
				<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',

            $content
        );

        $content = str_replace(
            '<# } else if ( \'image\' === data.type && data.sizes ) { #>',
            '<# } else if ( \'svg+xml\' === data.subtype ) { #>
				<div class="centered">
					<img src="{{ data.url }}" class="thumbnail" draggable="false" />
				</div>
			<# } else if ( \'image\' === data.type && data.sizes ) { #>',

            $content
        );

        return $content;

    }

    /**
     * Add the featured image column to the Wordpress tables.
     *
     * @param $columns
     *
     * @return array
     */
    public function customColumns( $columns ) {

        $offset = array_search( 'date', array_keys( $columns ) );

        return array_merge(
            array_slice( $columns, 0, $offset ),
            [ 'featured_image' => __( 'Featured Image' ) ],
            array_slice( $columns, $offset, null )
        );

    }

    /**
     * Prints data in a column.
     *
     * @param $column
     * @param $postId
     *
     * @return void
     */
    public function customColumnsData( $column, $postId ) {

        switch ( $column ) {
            case 'featured_image':
                echo the_post_thumbnail( 'thumbnail' );
                break;
        }

    }

    /**
     * {@inheritdoc}
     */
    public static function self() {

        if ( null === self::$instance ) {
            self::$instance = new static();
        }

        return self::$instance;

    }

}