<?php

/**
 * Singleton.php
 *
 * @see https://github.com/MBV-Media/bambee-core
 */

namespace MBVMedia\MetaBox;


use MBVMedia\Lib\ThemeView;

/**
 * Class MetaBox
 *
 * @package BambeeCore
 * @author Holger Terhoeven <h.terhoeven@mbv-media.com>
 * @licence MIT
 * @since 1.6.0
 * @see https://mbv-media.github.io/bambee-core-api/MBVMedia/MetaBox/MetaBox.html
 */
class MetaBox {

    /**
     * @var
     *
     * @ignore
     */
    private $id;

    /**
     * @var
     *
     * @ignore
     */
    private $title;

    /**
     * @var string
     *
     * @ignore
     */
    private $context;

    /**
     * @var ThemeView
     *
     * @ignore
     */
    private $template;

    /**
     * @var array
     *
     * @ignore
     */
    private $metaKeyList;

    /**
     * @var array
     *
     * @ignore
     */
    private $postTypeList;

    /**
     * @var string
     *
     * @ignore
     */
    private $nonceName;

    /**
     * @var string
     *
     * @ignore
     */
    private $nonceAction;

    /**
     * MetaBox constructor.
     *
     * @param $id
     * @param $title
     * @param $context
     * @param int $priority (optional)
     * @param ThemeView $template (optional)
     */
    public function __construct( $id, $title, $context = 'advanced', $priority = 10, ThemeView $template = null ) {

        $this->id = $id;
        $this->title = $title;
        $this->context = $context;

        if ( null === $template ) {
            $template = new ThemeView( 'partials/admin/meta-box-default.php' );
        }

        $this->template = $template;

        $this->metaKeyList = [];

        $this->postTypeList = [];

        $this->nonceName = get_class( $this );
        $this->nonceAction = 'save-' . $this->nonceName;

        add_action( 'add_meta_boxes', [ $this, 'actionAddMetaBox' ], $priority, 1 );
        add_action( 'save_post', [ $this, 'actionSavePost' ], 10, 3 );

    }

    /**
     * Get the meta key list.
     *
     * @return array
     */
    public function getMetaKeyList() {
        return $this->metaKeyList;
    }

    /**
     * Add a meta key
     *
     * @param MetaKey $metaKey
     *
     * @return void
     */
    public function addMetaKey( MetaKey $metaKey ) {

        $metaKey->getTemplate()->setArg( 'metaBox', $this );
        $this->metaKeyList[] = $metaKey;

    }

    /**
     * Add support for a specific post type.
     *
     * Note: By default the meta box will be added to every post type.
     * If you add supported post types, the meta box will only appear on these post types.
     *
     * @param $postType
     * @param int $priority (optional)
     *
     * @return void
     */
    public function addPostTypeSupport( $postType, $priority = 10 ) {

        $this->postTypeList[] = $postType;

        remove_action( 'add_meta_boxes', [ $this, 'actionAddMetaBox' ] );
        add_action( 'add_meta_boxes_' . $postType, [ $this, 'actionAddMetaBox' ], $priority, 1 );

    }

    /**
     * 'add_meta_boxes' action hook callback.
     *
     * @param $post
     *
     * @return void
     */
    public function actionAddMetaBox( $post ) {

        $postType = $post instanceof \WP_Post ? $post->post_type : $post;
        add_meta_box( $this->id, $this->title, [ $this, 'renderMetaBox' ], $postType, $this->context );

    }

    /**
     * Renders the meta box.
     *
     * @param $post
     *
     * @return void
     */
    public function renderMetaBox( $post ) {

        wp_nonce_field( $this->nonceAction, $this->nonceName );
        $this->template->setArg( 'metaKeyList', $this->getMetaKeyList() );
        echo $this->template->render();

    }

    /**
     * 'save_post' action hook callback.
     *
     * @param $postId
     * @param $post
     * @param $update
     *
     * @return void
     */
    public function actionSavePost( $postId, $post, $update ) {

        $postType = get_post_type_object( $post->post_type );
        $currentUserCanEditPostType = current_user_can( $postType->cap->edit_post, $postId );

        if ( wp_is_post_autosave( $postId ) || wp_is_post_revision( $postId ) || !$currentUserCanEditPostType ) {
            return;
        }

        $nonce = filter_input( INPUT_POST, $this->nonceName );
        if ( $nonce === null ) {
            $nonce = filter_input( INPUT_GET, $this->nonceName );
        }

        if ( !wp_verify_nonce( $nonce, $this->nonceAction ) ) {
            return;
        }

        foreach ( $this->metaKeyList as $metaKey ) {
            $metaKey->save( $postId );
        }

    }

    /**
     * Get the post meta.
     *
     * @param $metaKey
     * @param mixed|null $postId (optional)
     *
     * @return mixed
     */
    public function getPostMeta( $metaKey, $postId = null ) {

        if ( $postId === null ) {
            $postId = get_the_ID();
        }

        return get_post_meta( $postId, $metaKey, true );

    }

    /**
     * Get all post metas.
     *
     * @param mixed|null $postId (optional)
     *
     * @return array
     */
    public function getPostMetas( $postId = null ) {

        $postMetas = [];

        foreach ( $this->metaKeyList as $metaKey => $type ) {
            $postMetas[$metaKey] = $this->getPostMeta( $metaKey, $postId );
        }

        return $postMetas;

    }

}