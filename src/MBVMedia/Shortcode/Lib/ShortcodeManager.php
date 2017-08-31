<?php

/**
 * ShortcodeManager.php
 */

namespace MBVMedia\Shortcode\Lib;


/**
 * Class ShortcodeManager
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.0.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/Shortcode/Lib/ShortcodeManager.html
 */
class ShortcodeManager {

    /**
     * @var array
     *
     * @ignore
     */
    private $shortcodeList;

    /**
     * ShortocdeManager constructor.
     */
    public function __construct() {

        $this->shortcodeList = [];

    }

    /**
     * Load all shortcodes.
     *
     * @param array $path
     * @param mixed $namespace
     *
     * @return void
     */
    public function loadShortcodes( $path, $namespace ) {

        $shortcodeDir = scandir( $path );

        foreach ( $shortcodeDir as $shortcodeFile ) {
            if ( !is_dir( $path . $shortcodeFile ) ) {

                $class = $namespace . pathinfo( $shortcodeFile, PATHINFO_FILENAME );

                $this->shortcodeList[] = [
                    'class' => $class,
                    'file' => $shortcodeFile,
                    'tag' => $class::getShortcodeAlias(),
                ];
            }
        }

    }

    /**
     * Add all loaded shortcodes to Wordpress.
     *
     * @return void
     */
    public function addShortcodes() {

        foreach ( $this->shortcodeList as $shortcode ) {
            $class = $shortcode['class'];
            if ( is_callable( [ $class, 'addShortcode' ] ) ) {
                $class::addShortcode();
            }
        }

    }

    /**
     * Extend tinyMCE.
     *
     * @return void
     */
    public function extendTinyMCE() {

        add_action( 'admin_head', [ $this, 'printShortcodeData' ] );
        add_filter( 'mce_buttons', [ $this, 'tinyMceRegisterButton' ] );
        add_filter( 'mce_external_plugins', [ $this, 'tinyMceRegisterPlugin' ] );

    }

    /**
     * Make the shortcode data available in javascript.
     *
     * @return void
     */
    public function printShortcodeData() {

        ?>
        <script type="text/javascript">
          window.bambeeShortcodeList = [
              <?php foreach($this->shortcodeList as $shortcode) : ?>
              <?php $shortcodeObject = new $shortcode['class'](); ?>
            {
              tag: '<?php echo $shortcode['tag']; ?>',
              atts: <?php echo json_encode( $shortcodeObject->getSupportedAtts() ); ?>,
              descr: '<?php echo $shortcodeObject->getDescription(); ?>'
            },
              <?php endforeach; ?>
          ];
        </script>
        <?php

    }

    /**
     * Register the shortcode button with tinxMCE.
     *
     * @param $buttons
     * @global $current_screen
     *
     * @return mixed
     */
    public function tinyMceRegisterButton( $buttons ) {

        global $current_screen; //  WordPress contextual information about where we are.

        $type = $current_screen->post_type;

        if ( is_admin() && ( $type == 'post' || $type == 'page' ) ) {
            array_push( $buttons, 'separator', 'ShortcodeSelector' );
        }

        return $buttons;

    }

    /**
     * Register the shortcode plugin with tinyMCE.
     *
     * @param $pluginArray
     *
     * @return mixed
     */
    public function tinyMceRegisterPlugin( $pluginArray ) {

        $relativePath = str_replace( realpath( get_template_directory() ), '', realpath( dirname( __FILE__ ) ) );
        $relativePath = str_replace( '\\', '/', $relativePath );
        $url = get_template_directory_uri() . $relativePath;
        $pluginArray['ShortcodeSelector'] = $url . '/tinyMcePlugin.js';
        return $pluginArray;

    }

}