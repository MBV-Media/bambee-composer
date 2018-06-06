<?php

/**
 * BambeeWebsite.php
 *
 * @see https://github.com/MBV-Media/bambee-core
 */

namespace MBVMedia;


use MBVMedia\Lib\ThemeView;
use MBVMedia\ThemeCustomizer\ThemeCustommizerComments;

/**
 * The class representing the website (user frontend).
 *
 * @package BambeeCore
 * @author R4c00n <marcel.kempf93@gmail.com>
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.5.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/BambeeWebsite.html
 */
abstract class BambeeWebsite extends BambeeBase {

    /**
     * @var array
     *
     * @since 1.0.0
     * @ignore
     */
    private $scripts;

    /**
     * @var array
     *
     * @since 1.0.0
     * @ignore
     */
    private $localizedScripts;

    /**
     * @var array
     *
     * @since 1.0.0
     * @ignore
     */
    private $styles;

    /**
     * @var string
     *
     * @since 1.1.0
     * @ignore
     */
    private $commentPaginationNextText;

    /**
     * @var string
     *
     * @since 1.1.0
     * @ignore
     */
    private $commentPaginationPrevText;

    /**
     * @var string
     *
     * @since 1.1.0
     * @ignore
     */
    private $commentPaginationPageTemplate;

    /**
     * @var BambeeWebsite
     *
     * @since 1.5.0
     * @ignore
     */
    private static $instance = null;

    /**
     * BambeeWebsite constructor.
     *
     * @since 1.0.0
     */
    protected function __construct() {

        $this->scripts = [];
        $this->localizedScripts = [];
        $this->styles = [];

        $this->commentPaginationNextText = __( 'Next &raquo;', TextDomain );
        $this->commentPaginationPrevText = __( '&laquo; Prev', TextDomain );
        $this->commentPaginationPageTemplate = '<li>%s</li>';

        # Gulp livereload (development only)
        if ( WP_DEBUG ) {
            $this->addScript( 'livereload', '//localhost:35729/livereload.js' );
        }

    }

    /**
     * Get the comment pagination text for the next-button.
     *
     * @return string
     *
     * @since 1.4.0
     */
    public function getCommentPaginationNextText() {

        return $this->commentPaginationNextText;

    }

    /**
     * Set the comment pagination text for the next-button.
     *
     * @param string $commentPaginationNextText
     *
     * @return void
     *
     * @since 1.4.0
     */
    public function setCommentPaginationNextText( $commentPaginationNextText ) {

        $this->commentPaginationNextText = $commentPaginationNextText;

    }

    /**
     * Get the comment pagination text for the previous-button.
     *
     * @return string
     *
     * @since 1.4.0
     */
    public function getCommentPaginationPrevText() {

        return $this->commentPaginationPrevText;

    }

    /**
     * Set the comment pagination text for the previous-button.
     *
     * @param string $commentPaginationPrevText
     *
     * @since 1.4.0
     */
    public function setCommentPaginationPrevText( $commentPaginationPrevText ) {

        $this->commentPaginationPrevText = $commentPaginationPrevText;

    }

    /**
     * Get the comment pagination template.
     *
     * @return string
     *
     * @since 1.4.0
     */
    public function getCommentPaginationPageTemplate() {

        return $this->commentPaginationPageTemplate;

    }

    /**
     * Set the comment pagination template.
     *
     * @param string $commentPaginationPageTemplate
     *
     * @since 1.4.0
     */
    public function setCommentPaginationPageTemplate( $commentPaginationPageTemplate ) {

        $this->commentPaginationPageTemplate = $commentPaginationPageTemplate;

    }

    /**
     * {@inheritdoc}
     */
    public function addActions() {

        add_action( 'init', [ $this, 'disableEmojis' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueScripts' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueLocalizeScripts' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueStyles' ] );
        add_action( 'wp_footer', [ $this, 'printGoogleAnalyticsCode' ] );
        add_action( 'wpcf7_before_send_mail', [ $this, 'addCF7DefaultRecipient' ] );

        if ( get_theme_mod( 'bambee_comment_textbox_position' ) ) {
            add_filter( 'comment_form_fields', [ $this, 'moveCommentFieldToBottom' ] );
        }

    }

    /**
     * {@inheritdoc}
     */
    public function addFilters() {

        add_filter( 'show_admin_bar', '__return_false' );

    }

    /**
     * Register additional scripts.
     *
     * @return void
     */
    public function addScripts() {

        $this->addScript( 'comment-reply', false );
        $this->addScript(
            'vendor',
            ThemeUrl . '/js/vendor.min.js',
            [ 'jquery' ],
            false,
            true
        );
        $this->addScript(
            'main',
            ThemeUrl . '/js/main.min.js',
            [ 'jquery' ],
            false,
            true
        );

    }

    /**
     * Register additional styles.
     *
     * @return void
     */
    public function addStyles() {

        $this->addStyle( 'main', ThemeUrl . '/css/main.min.css' );

    }

    /**
     * Register a script.
     *
     * @param $handle
     * @param $src
     * @param array $deps (optional)
     * @param bool $ver (optional)
     * @param bool $inFooter (optional)
     *
     * @return void
     *
     * @since 1.4.0
     */
    public function addScript( $handle, $src, $deps = [], $ver = false, $inFooter = false ) {

        $this->scripts[] = [
            'handle' => $handle,
            'src' => $src,
            'deps' => $deps,
            'ver' => $ver,
            'in_footer' => $inFooter,
        ];

    }

    /**
     * Register a localized script.
     *
     * @param $handle
     * @param $name
     * @param array $data
     *
     * @return void
     *
     * @since 1.4.0
     */
    public function addLocalizedScript( $handle, $name, array $data ) {

        $this->localizedScripts[] = [
            'handle' => $handle,
            'name' => $name,
            'data' => $data,
        ];

    }

    /**
     * Regsiter a style.
     *
     * @param $handle
     * @param $src
     * @param array $deps (optional)
     * @param bool $ver (optional)
     * @param string $media (optional)
     *
     * @return void
     *
     * @since 1.4.0
     */
    public function addStyle( $handle, $src, $deps = [], $ver = false, $media = 'all' ) {

        $this->styles[] = [
            'handle' => $handle,
            'src' => $src,
            'deps' => $deps,
            'ver' => $ver,
            'media' => $media,
        ];

    }

    /**
     * Disable the Wordpress default emojis.
     *
     * @return void
     */
    public function disableEmojis() {

        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

    }

    /**
     * Customize the comment list.
     *
     * @param string $comment
     * @param array $args
     * @param int $depth
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function commentList( $comment, $args, $depth ) {

        echo $this->getCommentList( $comment, $args, $depth );

    }

    /**
     * Get the comment list.
     *
     * @param $comment
     * @param $args
     * @param $depth
     *
     * @return string
     *
     * @since 1.4.2
     */
    public function getCommentList( $comment, $args, $depth ) {

        $GLOBALS['comment'] = $comment;

        $tag = ( 'div' == $args['style'] ) ? 'div' : 'li';
        $addBelow = 'comment';

        $commentListTemplate = new ThemeView( '/partials/comment-list.php', [
            'comment' => $comment,
            'arguments' => $args,
            'depth' => $depth,
            'tag' => $tag,
            'addBelow' => $addBelow,
        ] );
        return $commentListTemplate->render();

    }

    /**
     * Renders the comment pagination.
     *
     * @return void
     *
     * @since 1.1.0
     */
    public function commentPagination() {

        echo $this->getCommentPagination();

    }

    /**
     * Get the comment pagination.
     *
     * @return string
     *
     * @since 1.4.2
     */
    public function getCommentPagination() {

        $pagination = paginate_comments_links( [
            'echo' => false,
            'mid_size' => 2,
            'end_size' => 3,
            'type' => 'array',
            'add_fragment' => '',
            'next_text' => $this->commentPaginationNextText,
            'prev_text' => $this->commentPaginationPrevText,
        ] );

        $paginationPages = '';
        $paginationPrev = '';
        $paginationNext = '';

        if ( !empty( $pagination ) ) {
            $count = 0;

            foreach ( $pagination as $pageData ) {
                if ( is_numeric( strip_tags( $pageData ) )
                    || strip_tags( $pageData ) === '&hellip;'
                ) {
                    $paginationPages .= sprintf( $this->commentPaginationPageTemplate, $pageData );
                } else {
                    if ( $count > 0 ) {
                        $paginationNext = $pageData;
                    } else {
                        $paginationPrev = $pageData;
                    }
                }

                ++$count;
            }
        }

        $template = new ThemeView( '/partials/comment-pagination.php', [
            'paginationPrev' => $paginationPrev,
            'paginationPages' => $paginationPages,
            'paginationNext' => $paginationNext,
        ] );
        return $template->render();

    }


    /**
     * Set a default recipient to contact form 7 mails.
     *
     * @param $cf7
     *
     * @since 1.4.2
     */
    public function addCF7DefaultRecipient( $cf7 ) {

        $mail = $cf7->prop( 'mail' );

        if ( !empty( $mail['recipient'] ) ) {
            return;
        }

        $mail['recipient'] = get_bloginfo( 'admin_email' );
        $cf7->set_properties( [
            'mail' => $mail,
        ] );

    }

    /**
     * Moves the comment textfield to the bottom of the form.
     *
     * @param $fields
     *
     * @return mixed
     */
    public function moveCommentFieldToBottom( $fields ) {

        $commentField = $fields['comment'];
        unset( $fields['comment'] );
        $fields['comment'] = $commentField;
        return $fields;

    }

    /**
     * Enqueue all added JS files.
     *
     * @return void
     *
     * @since 1.4.2
     */
    public function enqueueScripts() {

        if ( !empty( $this->scripts ) ) {
            foreach ( $this->scripts as $script ) {
                wp_enqueue_script(
                    $script['handle'],
                    $script['src'],
                    $script['deps'],
                    $script['ver'],
                    $script['in_footer']
                );
            }
        }

    }

    /**
     * Enqueue all added localize JS files.
     *
     * @return void
     *
     * @since 1.4.2
     */
    public function enqueueLocalizeScripts() {

        if ( !empty( $this->localizedScripts ) ) {
            foreach ( $this->localizedScripts as $localized_script ) {
                wp_localize_script(
                    $localized_script['handle'],
                    $localized_script['name'],
                    $localized_script['data']
                );
            }
        }

    }

    /**
     * Enqueue all added CSS files.
     *
     * @return void
     *
     * @since 1.4.2
     */
    public function enqueueStyles() {

        if ( !empty( $this->styles ) ) {
            foreach ( $this->styles as $style ) {
                wp_enqueue_style(
                    $style['handle'],
                    $style['src'],
                    $style['deps'],
                    $style['ver'],
                    $style['media']
                );
            }
        }

    }

    /**
     * Prints the Google Analytics code if a tracking code is set.
     *
     * @return void
     *
     * @since 1.4.2
     */
    public function printGoogleAnalyticsCode() {

        if ( WP_DEBUG ) {
            return;
        }

        $googleTrackingId = get_option( 'bambee_google_analytics_tracking_id' );
        if ( !empty( $googleTrackingId ) ) {
            ?>
            <script>
              (function (b, o, i, l, e, r) {
                b.GoogleAnalyticsObject = l;
                b[l] || (b[l] =
                  function () {
                    (b[l].q = b[l].q || []).push(arguments)
                  });
                b[l].l = +new Date;
                e = o.createElement(i);
                r = o.getElementsByTagName(i)[0];
                e.src = 'https://www.google-analytics.com/analytics.js';
                r.parentNode.insertBefore(e, r)
              }(window, document, 'script', 'ga'));
              ga('create', '<?php echo $googleTrackingId; ?>', 'auto');
              ga('set', 'anonymizeIp', true);
              ga('send', 'pageview');
            </script>
            <?php
        }

    }

    /**
     * Performs the Wordpress main loop.
     *
     * @param ThemeView $partial
     * @param ThemeView|null $noPosts
     *
     * @return void
     */
    public function mainLoop( ThemeView $partial, ThemeView $noPosts = null ) {

        if ( have_posts() ) {
            while ( have_posts() ) {
                the_post();
                echo $partial->render();
            }
        } elseif ( null !== $noPosts ) {
            echo $noPosts->render();
        }

    }

    /**
     * Performs a custom loop.
     *
     * @param ThemeView $partial
     * @param array $queryArgs
     * @param ThemeView|null $noPosts
     *
     * @return void
     */
    public function customLoop( ThemeView $partial, array $queryArgs = [], ThemeView $noPosts = null ) {

        $theQuery = new \WP_Query( $queryArgs );

        if ( $theQuery->have_posts() ) {

            $partial->setArg( 'theQuery', $theQuery );

            while ( $theQuery->have_posts() ) {

                $theQuery->the_post();
                echo $partial->render();
            }
        } elseif ( null !== $noPosts ) {
            $noPosts->setArg( 'theQuery', $theQuery );
            echo $noPosts->render();
        }

        wp_reset_postdata();

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
