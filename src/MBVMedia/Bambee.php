<?php

/**
 * Bambee.php
 *
 * @package BambeeCore
 */

namespace MBVMedia;


if ( !defined( 'TextDomain' ) ) {

    /**
     * Used to load the Wordpress theme textdomain.
     * Should be used in every translation function __, _e, etc. for proper string translation.
     */
    define( 'TextDomain', 'bambee' );

}

if ( !defined( 'ThemeDir' ) ) {

    /**
     * The theme's full directory path.
     */
    define( 'ThemeDir', get_stylesheet_directory() );

}

if ( !defined( 'ThemeUrl' ) ) {

    /**
     * The theme's full URL.
     */
    define( 'ThemeUrl', get_stylesheet_directory_uri() );

}


use MBVMedia\ControlledTemplate\CookieControlledTemplate;
use MBVMedia\Lib\ThemeView;
use MBVMedia\Shortcode\Lib\ShortcodeManager;
use MBVMedia\ThemeCustomizer\Panel;
use MBVMedia\ThemeCustomizer\Section;
use MBVMedia\ThemeCustomizer\Setting;
use MBVMedia\ThemeCustomizer\ThemeCustommizer;

/**
 * The class representing both website (user frontend) and WordPress admin.
 *
 * @package BambeeCore
 * @author R4c00n <marcel.kempf93@gmail.com>
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.0.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/Bambee.html
 */
abstract class Bambee extends BambeeBase {

    /**
     * @var string
     *
     * @ignore
     */
    private $dynamicFrontpageInterval;

    /**
     * @var array
     *
     * @since 1.0.0
     * @ignore
     */
    private $postThumbnail;

    /**
     * @var array
     *
     * @ignore
     */
    private $customLogo;

    /**
     * @var array
     *
     * @ignore
     */
    private $customHeader;

    /**
     * @var array
     *
     * @since 1.0.0
     * @ignore
     */
    private $menuList;

    /**
     * @var array
     *
     * @since 1.4.2
     * @ignore
     */
    private $postTypeList;

    /**
     * @var ShortcodeManager
     *
     * @since 1.4.2
     * @ignore
     */
    private $shortcodeManager;

    /**
     * @var ThemeCustommizer
     * @since 1.7.0
     * @ignore
     */
    private $themeCustomizer;

    /**
     * @var Bambee
     *
     * @since 1.5.0
     * @ignore
     */
    private static $instance = null;

    /**
     * Bambee constructor.
     *
     * @since 1.0.0
     */
    protected function __construct() {

        $this->loadThemeTextdomain();

        $this->dynamicFrontpageInterval = '24:00:00';

        $this->postThumbnail = [
            'width' => 624,
            'height' => 999,
            'crop' => false,
        ];

        $this->customLogo = [
            'width' => 300,
            'height' => 200,
            'flex-width' => true,
            'flex-height' => true,
        ];

        $this->customHeader = [
            'width' => 1200,
            'height' => 450,
            'flex-width' => true,
            'flex-height' => true,
        ];

        $this->menuList = [];

        $this->postTypeList = [];

        $this->addPostTypeGallery();

        $this->initShortcodes();
        $this->initDynamicFrontpage();
        $this->initCookieNotice();
        $this->initThemeCustomizer();

    }

    /**
     * {@inheritdoc}
     */
    public function addActions() {

        add_action( 'init', [ $this, 'actionInit' ] );
        add_action( 'after_setup_theme', [ $this, 'actionAfterSetupTheme' ] );

    }

    /**
     * {@inheritdoc}
     */
    public function addFilters() {

        // TODO: Implement addFilters() method.

    }

    /**
     * "init" action hook callback.
     *
     * @return void
     *
     * @since 1.4.0
     */
    public function actionInit() {

        $this->registerPostTypes();

    }

    /**
     * "after_setup_theme" action hook callback.
     *
     * @return void
     *
     * @since 1.4.0
     */
    public function actionAfterSetupTheme() {

        $this->addThemeSupportFeaturedImages();
        $this->addThemeSupportCustomLogo();
        $this->addThemeSupportCustomHeader();
        $this->addThemeSupportCustomBackground();
        $this->addPostTypeSupportExcerpt();
        $this->registerMenus();

    }

    /**
     * @ignore
     */
    private function addPostTypeGallery() {

        $componentUrl = $this->getComponentUrl();
        $this->addPostType( 'gallery', [
            'labels' => [
                'name' => __( 'Galleries', TextDomain ),
                'singular_name' => __( 'Gallery', TextDomain ),
            ],
            'taxonomies' => [ 'category' ],
            'menu_icon' => $componentUrl . '/assets/img/icons/gallery.png',
            'public' => true,
            'has_archive' => true,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'capability_type' => 'post',
            'hierarchical' => true,
            'supports' => [
                'title',
                'author',
                'editor',
                'thumbnail',
                'trackbacks',
                'custom-fields',
                'revisions',
            ],
            'exclude_from_search' => true,
            'publicly_queryable' => true,
            'excerpt' => true,
        ] );

    }

    /**
     * Initializes the shorcode-manager and loads the shortcodes.
     *
     * @return void
     *
     * @since 1.6.0
     */
    public function initShortcodes() {

        $this->shortcodeManager = new ShortcodeManager();
        $this->shortcodeManager->loadShortcodes(
            dirname( __FILE__ ) . '/Shortcode/',
            '\MBVMedia\Shortcode\\'
        );
        $this->shortcodeManager->loadShortcodes(
            ThemeDir . '/lib/shortcode/',
            '\Lib\Shortcode\\'
        );

    }

    /**
     * Initializes the dynamic frontpage depending on the theme option "bambee_dynamic_front_page_show".
     *
     * @return void
     *
     * @since 1.6.0
     */
    public function initDynamicFrontpage() {

        if ( !get_theme_mod( 'bambee_dynamic_front_page_show', true ) ) {
            return;
        }

        $interval = get_theme_mod( 'bambee_dynamic_front_page_interval', $this->dynamicFrontpageInterval );
        $interval = empty( $interval ) ? $this->dynamicFrontpageInterval : $interval;
        $interval = strtotime( $interval ) - strtotime( 'TODAY' );

        $entranceOverlay = new CookieControlledTemplate(
            new ThemeView( 'partials/overlay-entrance.php' ),
            'enter',
            '.overlay-entry .js-enter',
            '.overlay-entry',
            $interval
        );
        $entranceOverlay->addActions();

    }

    /**
     * Initializes the cookie notice.
     *
     * @return void
     *
     * @since 1.6.0
     */
    public function initCookieNotice() {

        $cookieNotice = new CookieControlledTemplate(
            new ThemeView( 'partials/cookie-notice.php' ),
            'cookie',
            '.cookie-notice .js-hide',
            '.cookie-notice'
        );
        $cookieNotice->addActions();

    }

    /**
     * Initializes the theme customizer inclusively all customizable theme options:
     *
     * - bambee_dynamic_front_page_show
     * - bambee_dynamic_front_page_interval
     * - bambee_comment_textbox_position
     * - bambee_core_data_address
     * - bambee_core_data_email
     * - bambee_core_data_phone
     * - bambee_google_maps_latitude
     * - bambee_google_maps_longitude
     * - bambee_google_maps_zoom
     * - bambee_google_maps_api_key
     * - bambee_google_maps_styles
     * - bambee_google_analytics_tracking_id
     *
     * @return void
     *
     * @since 1.6.0
     */
    public function initThemeCustomizer() {

        $this->themeCustomizer = new ThemeCustommizer();
        $this->initThemeSettingsDynamicFrontPage();
        $this->initThemeSettingsComments();
        $this->initThemeSettingsCoreData();
        $this->initThemeSettingsGoogle();
        $this->themeCustomizer->register();

    }

    /**
     * Adds a "Dynamic frontpage" section to the Wordpress customizer and
     * initializes all customizable dynamic front-page theme settings:
     *
     * - bambee_dynamic_front_page_show
     * - bambee_dynamic_front_page_interval
     *
     * @return void
     *
     * @since 1.5.0
     */
    public function initThemeSettingsDynamicFrontPage() {

        $settingDynamicFrontpageShow = new Setting(
            'bambee_dynamic_front_page_show',
            [
                'default' => true,
            ],
            [
                'label' => __( 'Show frontpage-overlay', TextDomain ),
                'type' => 'checkbox',
            ]
        );

        $settingDynamicFrontpageInterval = new Setting(
            'bambee_dynamic_front_page_interval',
            [
                'default' => '',
            ],
            [
                'label' => __( 'Interval', TextDomain ),
                'description' => sprintf( __( 'Time after which the overlay is displayed again. (Default: %s)', TextDomain ), $this->dynamicFrontpageInterval ),
                'type' => 'text',
                'input_attrs' => [
                    'placeholder' => 'hh:mm:ss',
                ],
            ]
        );

        $sectionDynamicFrontpage = new Section( 'bambee_dynamic_front_page', [
            'title' => __( 'Dynamic frontpage', TextDomain ),
            'priority' => 120,
        ] );
        $sectionDynamicFrontpage->addSetting( $settingDynamicFrontpageShow );
        $sectionDynamicFrontpage->addSetting( $settingDynamicFrontpageInterval );

        $this->themeCustomizer->addSection( $sectionDynamicFrontpage );

    }

    /**
     * Adds a "Comments" section to the Wordpress customizer and
     * initializes all customizable comments theme settings:
     *
     * - bambee_comment_textbox_position
     *
     * @return void
     *
     * @since 1.5.0
     */
    public function initThemeSettingsComments() {

        $settingCommentTextboxPosition = new Setting(
            'bambee_comment_textbox_position',
            [
                'default' => false,
            ],
            [
                'label' => __( 'Move form textfield to the bottom', TextDomain ),
                'type' => 'checkbox',
            ]
        );

        $sectionComment = new Section( 'bambee_comment', [
            'title' => __( 'Comments' ),
            'priority' => 80,
        ] );
        $sectionComment->addSetting( $settingCommentTextboxPosition );

        $this->themeCustomizer->addSection( $sectionComment );

    }

    /**
     * Adds a "Core data" section to the Wordpress customizer and
     * initializes all customizable core data theme settings:
     *
     * - bambee_core_data_address
     * - bambee_core_data_email
     * - bambee_core_data_phone
     *
     * @return void
     *
     * @since 1.5.0
     */
    public function initThemeSettingsCoreData() {

        $settingCoreDataAddress = new Setting(
            'bambee_core_data_address',
            [
                'type' => 'option',
                'default' => '',
            ],
            [
                'label' => __( 'Address', TextDomain ),
                'type' => 'textarea',
            ]
        );

        $settingCoreDataEmail = new Setting(
            'bambee_core_data_email',
            [
                'type' => 'option',
                'default' => '',
            ],
            [
                'label' => __( 'E-Mail address', TextDomain ),
                'type' => 'text',
            ]
        );

        $settingCoreDataPhone = new Setting(
            'bambee_core_data_phone',
            [
                'type' => 'option',
                'default' => '',
            ],
            [
                'label' => __( 'Phone', TextDomain ),
                'type' => 'text',
            ]
        );

        $sectionCoreData = new Section( 'bambee_core_data_section', [
            'title' => __( 'Core data', TextDomain ),
            'priority' => 700,
            'description' => __(
                'You can use the [coredata]key[coredata]' .
                ' shortcode to display the core data field inside a post.',
                TextDomain
            ),
        ] );
        $sectionCoreData->addSetting( $settingCoreDataAddress );
        $sectionCoreData->addSetting( $settingCoreDataEmail );
        $sectionCoreData->addSetting( $settingCoreDataPhone );

        $this->themeCustomizer->addSection( $sectionCoreData );

    }

    /**
     * Adds a "Google" panel along with a "Maps" and "Analytics" section to the Wordpress customizer and
     * initializes all customizable core data theme settings:
     *
     * - bambee_google_maps_latitude
     * - bambee_google_maps_longitude
     * - bambee_google_maps_zoom
     * - bambee_google_maps_api_key
     * - bambee_google_maps_styles
     * - bambee_google_analytics_tracking_id
     *
     * @return void
     *
     * @since 1.5.0
     */
    public function initThemeSettingsGoogle() {

        $settingGoogleMapsLatitude = new Setting(
            'bambee_google_maps_latitude',
            [
                'type' => 'option',
                'default' => '',
            ],
            [
                'label' => __( 'Latitude', TextDomain ),
                'type' => 'text',
            ]
        );

        $settingGoogleMapsLongitude = new Setting(
            'bambee_google_maps_longitude',
            [
                'type' => 'option',
                'default' => '',
            ],
            [
                'label' => __( 'Longitude', TextDomain ),
                'type' => 'text',
            ]
        );

        $settingGoogleMapsZoom = new Setting(
            'bambee_google_maps_zoom',
            [
                'type' => 'option',
                'default' => 15,
            ],
            [
                'label' => __( 'Zoom', TextDomain ),
                'type' => 'number',
                'input_attrs' => [
                    'min' => 0,
                ],
            ]
        );

        $settingGoogleMapsApiKey = new Setting(
            'bambee_google_maps_api_key',
            [
                'type' => 'option',
                'default' => '',
            ],
            [
                'label' => __( 'API-Key', TextDomain ),
                'type' => 'text',
            ]
        );

        $settingGoogleMapsStyles = new Setting(
            'bambee_google_maps_styles',
            [
                'type' => 'option',
                'default' => '',
            ],
            [
                'label' => __( 'Styles', TextDomain ),
                'description' => sprintf( __( 'Copy the created %smap-style%s-JSON to the textarea.', TextDomain ), '<a href="https://mapstyle.withgoogle.com/" target="_blank">', '</a>' ),
                'type' => 'textarea',
            ]
        );

        $sectionGoogleMaps = new Section( 'bambee_google_maps_section', [
            'title' => __( 'Maps', TextDomain ),
        ] );
        $sectionGoogleMaps->addSetting( $settingGoogleMapsLatitude );
        $sectionGoogleMaps->addSetting( $settingGoogleMapsLongitude );
        $sectionGoogleMaps->addSetting( $settingGoogleMapsZoom );
        $sectionGoogleMaps->addSetting( $settingGoogleMapsApiKey );
        $sectionGoogleMaps->addSetting( $settingGoogleMapsStyles );

        $settingGoogleAnalyticsTracktingId = new Setting(
            'bambee_google_analytics_tracking_id',
            [
                'type' => 'option',
            ],
            [
                'label' => __( 'Trackting-ID', TextDomain ),
                'type' => 'text',
                'input_attrs' => [
                    'placeholder' => 'UA-XXXXX-X',
                ],
            ]
        );

        $sectionGoogleAnalytics = new Section( 'bambee_google_analytics_section', [
            'title' => __( 'Analytics', TextDomain ),
            'description' => __( 'After entering the tracking ID, the Google Analytics Script is automatically included.', TextDomain ),
        ] );
        $sectionGoogleAnalytics->addSetting( $settingGoogleAnalyticsTracktingId );

        $panelGoogle = new Panel( 'bambee_google_panel', [
            'priority' => 800,
            'title' => __( 'Google', TextDomain ),
            'description' => '',
        ] );
        $panelGoogle->addSection( $sectionGoogleMaps );
        $panelGoogle->addSection( $sectionGoogleAnalytics );

        $this->themeCustomizer->addPanel( $panelGoogle );

    }

    /**
     * Get the shortcode manager.
     *
     * @return ShortcodeManager
     *
     * @since 1.4.2
     */
    public function getShortcodeManager() {

        return $this->shortcodeManager;

    }

    /**
     * Get the theme customizer.
     *
     * @return ThemeCustommizer
     */
    public function getThemeCustomizer() {

        return $this->themeCustomizer;

    }

    /**
     * Get the featured image default sizes.
     *
     * @return array
     *
     * @since 1.7.0
     */
    public function getFeaturedImageDefaults() {

        return $this->postThumbnail;

    }

    /**
     * @param int $postThumbnailWidth
     *
     * @since 1.4.2
     * @deprecated Will be removed in 1.8.0.<br>Can now be set by theme option "bambee_post_thumbnail_width".
     */
    public function setPostThumbnailWidth( $postThumbnailWidth ) {

        $this->postThumbnail['width'] = $postThumbnailWidth;

    }

    /**
     * @param int $postThumbnailHeight
     *
     * @since 1.4.2
     * @deprecated Will be removed in 1.8.0.<br>Can now be set by theme option "bambee_post_thumbnail_height".
     */
    public function setPostThumbnailHeight( $postThumbnailHeight ) {

        $this->postThumbnail['height'] = $postThumbnailHeight;

    }

    /**
     * @param boolean $postThumbnailCrop
     *
     * @since 1.4.2
     * @deprecated Will be removed in 1.8.0.<br>Can now be set by theme option "bambee_post_thumbnail_corp".
     */
    public function setPostThumbnailCrop( $postThumbnailCrop ) {

        $this->postThumbnail['crop'] = $postThumbnailCrop;

    }

    /**
     * Adds an additional menu to register with Wordpress.
     *
     * @param $slug     An unique identifier.
     * @param $title    The displayed title.
     *
     * @return void
     *
     * @since 1.4.0
     */
    public function addMenu( $slug, $title ) {

        $this->menuList[$slug] = $title;

    }

    /**
     * Registers all menus, added with addMenu, with Wordpress.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function registerMenus() {

        register_nav_menus( $this->menuList );

    }

    /**
     * Adds an additional post type to register with Wordpress.
     *
     * @param $postType
     * @param array $args
     *
     * @return void
     *
     * @since 1.4.2
     */
    public function addPostType( $postType, array $args ) {

        $this->postTypeList[$postType] = $args;

    }

    /**
     * Register all post types, added with addPostType, with Wordpress.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function registerPostTypes() {

        foreach ( $this->postTypeList as $postType => $args ) {
            register_post_type( $postType, $args );
        }

    }

    /**
     * Returns url to compentents of bambee
     *
     * @return mixed
     *
     * @since 1.4.0
     */
    public function getComponentUrl() {

        // fix for windows path
        $fixedAbsPath = str_replace( '\\', '/', ABSPATH );
        $fixedDirName = str_replace( '\\', '/', dirname( __FILE__ ) );
        // replace absolute path with url
        $componentUrl = str_replace( $fixedAbsPath, get_bloginfo( 'wpurl' ) . '/', $fixedDirName );

        return $componentUrl;

    }

    /**
     * Loads the textdomain.
     *
     * @return void
     *
     * @since 1.4.0
     */
    public function loadThemeTextdomain() {

        $path = ThemeDir . '/languages';
        load_theme_textdomain( TextDomain, $path );

    }

    /**
     * Adds the theme support for featured images.
     *
     * @return void
     *
     * @since 1.7.0
     */
    public function addThemeSupportFeaturedImages() {

        add_theme_support( 'post-thumbnails' );

        $featuredImageSize = get_option( 'bambee_featured_images', $this->postThumbnail );

        set_post_thumbnail_size(
            $featuredImageSize['width'],
            $featuredImageSize['height'],
            $featuredImageSize['crop']
        );

    }

    /**
     * Adds the theme support for the "custom-logo".
     *
     * @return void
     *
     * @since 1.4.0
     */
    public function addThemeSupportCustomLogo() {

        add_theme_support( 'custom-logo', $this->customLogo );

    }

    /**
     * Adds the theme support for the "custom-header".
     *
     * @return void
     *
     * @since 1.4.0
     */
    public function addThemeSupportCustomHeader() {

        add_theme_support( 'custom-header', $this->customHeader );

    }

    /**
     * Adds the theme support for the "custom-background".
     *
     * @return void
     *
     * @since 1.4.0
     */
    public function addThemeSupportCustomBackground() {

        add_theme_support( 'custom-background' );

    }

    /**
     * Adds the post type support "excerpt" for pages.
     *
     * @return void
     *
     * @since 1.4.0
     */
    public function addPostTypeSupportExcerpt() {

        add_post_type_support( 'page', 'excerpt', true );

    }

    /**
     * Get the Bambee instance.
     *
     * @return static
     *
     * @since 1.5.0
     */
    public static function self() {

        if ( null === self::$instance ) {
            self::$instance = new static();
        }

        return self::$instance;

    }

}