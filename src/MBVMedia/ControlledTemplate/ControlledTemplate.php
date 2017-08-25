<?php
/**
 * ControlledTemplate.php
 */

namespace MBVMedia\ControlledTemplate;


use MBVMedia\Lib\ThemeView;

/**
 * Class ControlledTemplate
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.0.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/ControlledTemplate/ControlledTemplate.html
 */
abstract class ControlledTemplate {

    /**
     * @var ThemeView
     * @ignore
     */
    private $template;

    /**
     * @var string
     * @ignore
     */
    private $nonce;

    /**
     * @var string
     * @ignore
     */
    private $selectorOnClick;

    /**
     * @var string
     * @ignore
     */
    private $selectorContainer;

    /**
     * SessionControledTemplate constructor.
     *
     * @param $template string
     * @param $nonce string
     * @param $selectorOnClick string
     * @param $selectorContainer string
     *
     * @since 1.0.0
     */
    public function __construct( ThemeView $template, $nonce, $selectorOnClick, $selectorContainer ) {

        $this->template = $template;
        $this->nonce = $nonce;
        $this->selectorOnClick = $selectorOnClick;
        $this->selectorContainer = $selectorContainer;

    }

    /**
     * Registers the required actions with Wordpress.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addActions() {

        if ( is_admin() ) {
            $this->addAdminActions();
        } else {
            $this->addWebsiteActions();
        }

    }

    /**
     * @ignore
     */
    private function addWebsiteActions() {

        add_action( 'init', [ $this, 'checkForNonce' ] );
        add_action( 'wp_footer', [ $this, 'renderTemplate' ] );
        add_action( 'wp_footer', [ $this, 'printScript' ] );

    }

    /**
     * @ignore
     */
    private function addAdminActions() {

        add_action( 'wp_ajax_' . $this->nonce, [ $this, 'ajaxCallback' ] );
        add_action( 'wp_ajax_nopriv_' . $this->nonce, [ $this, 'ajaxCallback' ] );

    }

    /**
     * The ajax callback.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function ajaxCallback() {

        $nonce = filter_input( INPUT_POST, 'nonce' );

        if ( !defined( 'DOING_AJAX' ) || !DOING_AJAX || !wp_verify_nonce( $nonce, $this->nonce ) ) {
            return;
        }

        $this->hide();

        die();

    }

    /**
     * Renders the template.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function renderTemplate() {

        if ( $this->hidden() ) {
            return;
        }

        echo $this->template->render();

    }

    /**
     * Checks for the nonce sent with HTTP GET.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function checkForNonce() {

        $nonce = filter_input( INPUT_GET, $this->nonce );

        if ( $nonce === null ) {
            return;
        }

        $this->hide();

    }

    /**
     * Prints the required javascript to the HTML document.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function printScript() {

        if ( $this->hidden() ) {
            return;
        }

        ?>
        <script type="text/javascript">
          (function ($) {
            $('<?php echo $this->selectorOnClick; ?>').on('click', function (e) {
              var preventDefault = $(this).is('[data-prevent-default]');
              if (preventDefault) {
                e.preventDefault();
              }
              $('<?php echo $this->selectorContainer; ?>').addClass('hidden');
              $.ajax({
                type: 'post',
                dataType: 'json',
                url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                cache: false,
                data: {
                  action: '<?php echo $this->nonce; ?>',
                  nonce: '<?php echo wp_create_nonce( $this->nonce ); ?>'
                }
              });
              if (preventDefault) {
                return false;
              }
            });
          })(jQuery);
        </script>
        <?php

    }

    /**
     * Hides the template.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public abstract function hide();

    /**
     * Determines if the template is hidden.
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public abstract function hidden();

}