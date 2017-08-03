<?php

if( !defined( 'TextDomain' ) ) {
    define( 'TextDomain', 'bambee' );
}
if( !defined( 'ThemeDir' ) ) {
    define( 'ThemeDir', get_stylesheet_directory() );
}
if( !defined( 'ThemeUrl' ) ) {
    define( 'ThemeUrl', get_stylesheet_directory_uri() );
}

/**
 * @since 1.0.0
 * @author R4c00n <marcel.kempf93@gmail.com>
 * @licence MIT
 */
namespace MBVMedia;


use MBVMedia\ControlledTemplate\CookieControlledTemplate;
use MBVMedia\Lib\ThemeView;
use MBVMedia\Shortcode\Lib\ShortcodeManager;
use MBVMedia\ThemeCustomizer\Control;
use MBVMedia\ThemeCustomizer\Panel;
use MBVMedia\ThemeCustomizer\Section;
use MBVMedia\ThemeCustomizer\Setting;
use MBVMedia\ThemeCustomizer\ThemeCustommizer;


/**
 * The class representing both website (user frontend) and WordPress admin.
 *
 * @since 1.0.0
 * @author R4c00n <marcel.kempf93@gmail.com>
 * @licence MIT
 */
abstract class Bambee extends BambeeBase {

    /**
     * @since 1.0.0
     * @var array
     */
    private $postThumbnail;

    /**
     * @var array
     */
    private $customLogo;

    /**
     * @var array
     */
    private $customHeader;

    /**
     * @since 1.0.0
     * @var array
     */
    private $menuList;

    /**
     * @since 1.4.2
     * @var array
     */
    private $postTypeList;

    /**
     * @since 1.4.2
     * @var ShortcodeManager
     */
    private $shortcodeManager;

    /**
     * @since 1.7.0
     * @var ThemeCustommizer
     */
    private $themeCustomizer;

    /**
     * @since 1.5.0
     * @var Bambee
     */
    private static $instance = null;

    /**
     * @since 1.0.0
     * @return void
     */
    protected function __construct() {

        $this->loadThemeTextdomain();

        $this->postThumbnail = array(
            'width' => 624,
            'height' => 999,
            'crop' => false,
        );

        $this->customLogo = array(
            'width' => 300,
            'height' => 200,
            'flex-width' => true,
            'flex-height' => true,
        );

        $this->customHeader = array(
            'width' => 1200,
            'height' => 450,
            'flex-width' => true,
            'flex-height' => true,
        );

        $this->menuList = array();

        $this->postTypeList = array();

        $this->addPostTypeGallery();

        $this->initShortcodes();
        $this->initDynamicFrontpage();
        $this->initCookieNotice();
        $this->initThemeCustomizer();

    }

    /**
     *
     */
    public function addActions() {

        add_action( 'init', array( $this, 'actionInit' ) );
        add_action( 'after_setup_theme', array( $this, 'actionAfterSetupTheme' ) );

    }

    /**
     *
     */
    public function addFilters() {

        // TODO: Implement addFilters() method.

    }

    /**
     *
     */
    public function actionInit() {

        $this->registerPostTypes();

    }

    /**
     *
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
     *
     */
    private function addPostTypeGallery() {

        $componentUrl = $this->getComponentUrl();
        $this->addPostType( 'gallery', array(
            'labels' => array(
                'name' => __( 'Galleries', TextDomain ),
                'singular_name' => __( 'Gallery', TextDomain ),
            ),
            'taxonomies' => array( 'category' ),
            'menu_icon' => $componentUrl . '/assets/img/icons/gallery.png',
            'public' => true,
            'has_archive' => true,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'capability_type' => 'post',
            'hierarchical' => true,
            'supports' => array(
                'title',
                'author',
                'editor',
                'thumbnail',
                'trackbacks',
                'custom-fields',
                'revisions',
            ),
            'exclude_from_search' => true,
            'publicly_queryable' => true,
            'excerpt' => true,
        ) );

    }

    /**
     * Initializes the shorcode-manager and loads the shortcodes.
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
     */
    public function initDynamicFrontpage() {

        if( !get_theme_mod( 'bambee_dynamic_front_page_show', true ) ) {
            return;
        }

        $interval = get_theme_mod( 'bambee_dynamic_front_page_interval', '24:00:00' );
        $interval = empty( $interval ) ? '24:00:00' : $interval;
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
     * Initializes the coocie notice.
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
     * <ul>
     *  <li>bambee_dynamic_front_page_show</li>
     *  <li>bambee_dynamic_front_page_interval</li>
     *  <li>bambee_comment_textbox_position</li>
     *  <li>bambee_core_data_address</li>
     *  <li>bambee_core_data_email</li>
     *  <li>bambee_core_data_phone</li>
     *  <li>bambee_google_maps_latitude</li>
     *  <li>bambee_google_maps_longitude</li>
     *  <li>bambee_google_maps_zoom</li>
     *  <li>bambee_google_maps_api_key</li>
     *  <li>bambee_google_maps_styles</li>
     *  <li>bambee_google_analytics_tracking_id</li>
     * </ul>
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
     * <ul>
     *  <li>bambee_dynamic_front_page_show</li>
     *  <li>bambee_dynamic_front_page_interval</li>
     * </ul>
     */
    public function initThemeSettingsDynamicFrontPage() {

        $settingDynamicFrontpageShow = new Setting(
            'bambee_dynamic_front_page_show',
            array(
                'default' => true,
            ),
            array(
                'label' => __( 'Show frontpage-overlay', TextDomain ),
                'type' => 'checkbox',
            )
        );

        $settingDynamicFrontpageInterval = new Setting(
            'bambee_dynamic_front_page_interval',
            array(
                'default' => '',
            ),
            array(
                'label' => __( 'Anzeige Intervall', TextDomain ),
                'description' => __( 'Zeit nach der Das Overlay erneut angezeigt wird. (Standard: 24:00:00)', TextDomain ),
                'type' => 'text',
                'input_attrs' => array(
                    'placeholder' => 'hh:mm:ss',
                )
            )
        );

        $sectionDynamicFrontpage = new Section( 'bambee_dynamic_front_page', array(
            'title' => __( 'Dynamic frontpage', TextDomain ),
            'priority' => 120,
        ) );
        $sectionDynamicFrontpage->addSetting( $settingDynamicFrontpageShow );
        $sectionDynamicFrontpage->addSetting( $settingDynamicFrontpageInterval );

        $this->themeCustomizer->addSection( $sectionDynamicFrontpage );

    }

    /**
     * Adds a "Comments" section to the Wordpress customizer and
     * initializes all customizable comments theme settings:
     * <ul>
     *  <li>bambee_comment_textbox_position</li>
     * </ul>
     */
    public function initThemeSettingsComments() {

        $settingCommentTextboxPosition = new Setting(
            'bambee_comment_textbox_position',
            array(
                'default' => false,
            ),
            array(
                'label' => __( 'Move form textfield to the bottom', TextDomain ),
                'type' => 'checkbox',
            )
        );

        $sectionComment = new Section( 'bambee_comment', array(
            'title' => __( 'Comments' ),
            'priority' => 80,
        ) );
        $sectionComment->addSetting( $settingCommentTextboxPosition );

        $this->themeCustomizer->addSection( $sectionComment );

    }

    /**
     * Adds a "Core data" section to the Wordpress customizer and
     * initializes all customizable core data theme settings:
     * <ul>
     *  <li>bambee_core_data_address</li>
     *  <li>bambee_core_data_email</li>
     *  <li>bambee_core_data_phone</li>
     * </ul>
     */
    public function initThemeSettingsCoreData() {

        $settingCoreDataAddress = new Setting(
            'bambee_core_data_address',
            array(
                'type' => 'option',
                'default' => '',
            ),
            array(
                'label' => __( 'Address', TextDomain ),
                'type' => 'textarea',
            )
        );

        $settingCoreDataEmail = new Setting(
            'bambee_core_data_email',
            array(
                'type' => 'option',
                'default' => '',
            ),
            array(
                'label' => __( 'E-Mail address', TextDomain ),
                'type' => 'text',
            )
        );

        $settingCoreDataPhone = new Setting(
            'bambee_core_data_phone',
            array(
                'type' => 'option',
                'default' => '',
            ),
            array(
                'label' => __( 'Phone', TextDomain ),
                'type' => 'text',
            )
        );

        $sectionCoreData = new Section( 'bambee_core_data_section', array(
            'title' => __( 'Core data', TextDomain ),
            'priority' => 700,
            'description' => __(
                'You can use the [coredata]key[coredata]' .
                ' shortcode to display the core data field inside a post.',
                TextDomain
            )
        ) );
        $sectionCoreData->addSetting( $settingCoreDataAddress );
        $sectionCoreData->addSetting( $settingCoreDataEmail );
        $sectionCoreData->addSetting( $settingCoreDataPhone );

        $this->themeCustomizer->addSection( $sectionCoreData );

    }

    /**
     * Adds a "Google" panel along with a "Maps" and "Analytics" section to the Wordpress customizer and
     * initializes all customizable core data theme settings:
     * <ul>
     *  <li>bambee_google_maps_latitude</li>
     *  <li>bambee_google_maps_longitude</li>
     *  <li>bambee_google_maps_zoom</li>
     *  <li>bambee_google_maps_api_key</li>
     *  <li>bambee_google_maps_styles</li>
     *  <li>bambee_google_analytics_tracking_id</li>
     * </ul>
     */
    public function initThemeSettingsGoogle() {

        $settingGoogleMapsLatitude = new Setting(
            'bambee_google_maps_latitude',
            array(
                'type' => 'option',
                'default' => '',
            ),
            array(
                'label' => __( 'Latitude', TextDomain ),
                'type' => 'text',
            )
        );

        $settingGoogleMapsLongitude = new Setting(
            'bambee_google_maps_longitude',
            array(
                'type' => 'option',
                'default' => '',
            ),
            array(
                'label' => __( 'Longitude', TextDomain ),
                'type' => 'text',
            )
        );

        $settingGoogleMapsZoom = new Setting(
            'bambee_google_maps_zoom',
            array(
                'type' => 'option',
                'default' => 15,
            ),
            array(
                'label' => __( 'Zoom', TextDomain ),
                'type' => 'number',
                'input_attrs' => array(
                    'min' => 0,
                ),
            )
        );

        $settingGoogleMapsApiKey = new Setting(
            'bambee_google_maps_api_key',
            array(
                'type' => 'option',
                'default' => '',
            ),
            array(
                'label' => __( 'API-Key', TextDomain ),
                'type' => 'text',
            )
        );

        $settingGoogleMapsStyles = new Setting(
            'bambee_google_maps_styles',
            array(
                'type' => 'option',
                'default' => '',
            ),
            array(
                'label' => __( 'Styles', TextDomain ),
                'description' => sprintf( __( 'Das erstellte %sMap-Style%s JSON in das Textfeld kopieren.', TextDomain ), '<a href="https://mapstyle.withgoogle.com/" target="_blank">', '</a>' ),
                'type' => 'textarea',
            )
        );

        $sectionGoogleMaps = new Section( 'bambee_google_maps_section', array(
            'title' => __( 'Maps', TextDomain ),
        ) );
        $sectionGoogleMaps->addSetting( $settingGoogleMapsLatitude );
        $sectionGoogleMaps->addSetting( $settingGoogleMapsLongitude );
        $sectionGoogleMaps->addSetting( $settingGoogleMapsZoom );
        $sectionGoogleMaps->addSetting( $settingGoogleMapsApiKey );
        $sectionGoogleMaps->addSetting( $settingGoogleMapsStyles );

        $settingGoogleAnalyticsTracktingId = new Setting(
            'bambee_google_analytics_tracking_id',
            array(
                'type' => 'option',
            ),
            array(
                'label' => __( 'Trackting-ID', TextDomain ),
                'type' => 'text',
                'input_attrs' => array(
                    'placeholder' => 'UA-XXXXX-X',
                ),
            )
        );

        $sectionGoogleAnalytics = new Section( 'bambee_google_analytics_section', array(
            'title' => __( 'Analytics', TextDomain ),
            'description' => __( 'Nach Eingabe der Tracking-ID wird das Google Analytics Script automatisch eingebunden.', TextDomain ),
        ) );
        $sectionGoogleAnalytics->addSetting( $settingGoogleAnalyticsTracktingId );

        $panelGoogle = new Panel( 'bambee_google_panel', array(
            'priority'       => 800,
            'title'          => __( 'Google', TextDomain ),
            'description'    => '',
        ) );
        $panelGoogle->addSection( $sectionGoogleMaps );
        $panelGoogle->addSection( $sectionGoogleAnalytics );

        $this->themeCustomizer->addPanel( $panelGoogle );

    }

    /**
     * Get the shortcode manager.
     *
     * @since 1.4.2
     * @return ShortcodeManager
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
     * @return array
     */
    public function getFeaturedImageDefaults() {
        return $this->postThumbnail;
    }

    /**
     * @deprecated Will be removed in 1.8.0.<br>Can now be set by theme option "bambee_post_thumbnail_width".
     * @since 1.4.2
     *
     * @param int $postThumbnailWidth
     */
    public function setPostThumbnailWidth( $postThumbnailWidth ) {

        $this->postThumbnail['width'] = $postThumbnailWidth;

    }

    /**
     * @deprecated Will be removed in 1.8.0.<br>Can now be set by theme option "bambee_post_thumbnail_height".
     * @since 1.4.2
     *
     * @param int $postThumbnailHeight
     */
    public function setPostThumbnailHeight( $postThumbnailHeight ) {
        $this->postThumbnail['height'] = $postThumbnailHeight;
    }

    /**
     * @deprecated Will be removed in 1.8.0.<br>Can now be set by theme option "bambee_post_thumbnail_corp".
     * @since 1.4.2
     *
     * @param boolean $postThumbnailCrop
     */
    public function setPostThumbnailCrop( $postThumbnailCrop ) {

        $this->postThumbnail['crop'] = $postThumbnailCrop;

    }

    /**
     * Adds an additional menu to register in Wordpress.
     *
     * @since 1.4.0
     *
     * @param $slug
     * @param $title
     */
    public function addMenu( $slug, $title ) {

        $this->menuList[$slug] = $title;

    }

    /**
     * Registers all previously added menus.
     *
     * @since 1.0.0
     * @return void
     */
    public function registerMenus() {

        register_nav_menus( $this->menuList );

    }

    /**
     * Adds an additional post type to register in Wordpress.
     *
     * @since 1.4.2
     *
     * @param $postType
     * @param array $args
     */
    public function addPostType( $postType, array $args ) {

        $this->postTypeList[ $postType ] = $args;

    }

    /**
     * Register all previously added post types.
     *
     * @since 1.0.0
     * @return void
     */
    public function registerPostTypes() {

        foreach( $this->postTypeList as $postType => $args ) {
            register_post_type( $postType, $args );
        }

    }

    /**
     * Returns url to compentents of bambee
     *
     * @return mixed
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
     * Action-hook callbacks
     */

    /**
     *
     */
    public function loadThemeTextdomain() {

        $path = ThemeDir . '/languages';
        load_theme_textdomain( TextDomain, $path );

    }

    /**
     *
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
     *
     */
    public function addThemeSupportCustomLogo() {

        add_theme_support( 'custom-logo', $this->customLogo );

    }

    /**
     *
     */
    public function addThemeSupportCustomHeader() {

        add_theme_support( 'custom-header', $this->customHeader );

    }

    /**
     *
     */
    public function addThemeSupportCustomBackground() {

        add_theme_support( 'custom-background' );

    }

    /**
     *
     */
    public function addPostTypeSupportExcerpt() {

        add_post_type_support( 'page', 'excerpt', true );

    }

    /**
     * @return static
     */
    public static function self() {

        if( null === self::$instance ) {
            self::$instance = new static();
        }

        return self::$instance;

    }
}