<?php

/**
 * To show metabox at side bar of post add page.This is for both Sample and Sample Cloud APIs.
 * @package    wordpress
 * @author     Sample
 */
class Meta_Box_Page
{
                protected $dcuname;
                protected $dcpass;
                
                var $version = '1.2.3';
                //Initializing methods on creating their objects
                public function __construct( $dcuname = null, $dcpass = null )
                {
                                $this->dcuname = $dcuname;
                                $this->dcpass  = $dcpass;
                                global $wp_version;
                                add_action( 'admin_menu', array(
                                                 $this,
                                                'sp_add_meta_box' 
                                ) );
                                //Menu to get Sample Cloud videos
                                add_action( 'wp_ajax_sp_get_my_dmc_video_metabox_tab_videos', array(
                                                 $this,
                                                'sp_get_my_dmc_video_metabox_tab_videos' 
                                ) );
                                //Menu to get Sample My videos
                                add_action( 'wp_ajax_sp_get_my_dm_video_metabox_tab_videos', array(
                                                 $this,
                                                'sp_get_my_dm_video_metabox_tab_videos' 
                                ) );
                                add_action( 'wp_ajax_sp_check_both_connection', array(
                                                 $this,
                                                'sp_check_both_connection' 
                                ) );
                                add_shortcode( 'dmvideo', array(
                                                 $this,
                                                'sp_dmvideo_func' 
                                ) );
                                add_action( 'wpuf_add_post_form_tags', array(
                                                 $this,
                                                'sp_metabox_callback' 
                                ), 10, 'top' );
                }
                
                //Callback for wp_ajax_sp_check_both_connection
                public function sp_check_both_connection( )
                {
                                global $user_meta;
                                global $dm_session_store;
                                $dm  = $dm_session_store;
                                $dmc = $user_meta;
                                if ( !empty( $dm ) && !empty( $dmc ) ) {
                                                $auth = 'BOTH_CONNECTED';
                                } else if ( empty( $dm ) && !empty( $dmc ) ) {
                                                $auth = 'ONLY_DMC_CONNECTED';
                                } else if ( !empty( $dm ) && empty( $dmc ) ) {
                                                $auth = 'ONLY_DM_CONNECTED';
                                } else if ( empty( $dm ) && empty( $dmc ) ) {
                                                $auth = 'BOTH_DISCONNECTED';
                                }
                                echo $auth;
                                die;
                }
                
                //Callback method for Sample video gallery page handler
                
                public function sp_add_meta_box( )
                {
                                sp_add_meta_box( 'sample_plugin_metabox', __( 'Sample Plug-in', 'customgallery' ), array(
                                                 $this,
                                                'sp_metabox_callback' 
                                ), 'post', 'side', 'high' );
                }
                
                //Callback Method for Sample video detail page menu
                public function sp_metabox_callback( )
                {
                                require_once( SAMPLE_DIR . '/common/metabox-handler.php' );
                }
                
                //Callback method for sp_get_my_dmc_video_metabox_tab_videos
                public function sp_get_my_dmc_video_metabox_tab_videos( )
                {
                                $pn           = ( isset( $_POST['pagenumber'] ) ) ? $_POST['pagenumber'] : 1;
                                $search_title  = ( isset( $_POST['title'] ) && $_POST['title'] != 'notitle' ) ? $_POST['title'] : '';
                                $items_per_page = 5;
                                $sample_cloud = new Sample_Cloud_OwnMethod( $this->dcuname, $this->dcpass );
                                $dmcvideos    = $sample_cloud->sp_get_sample_cloud_videos( (int) $pn, $items_per_page, $search_title );
                                print json_encode( $dmcvideos );
                                exit;
                }
                
                //Callback method for sp_get_my_dm_video_metabox_tab_videos
                public function sp_get_my_dm_video_metabox_tab_videos( )
                {
                                $pn           = ( isset( $_POST['pagenumber'] ) ) ? $_POST['pagenumber'] : 1;
                                $search_title  = ( isset( $_POST['title'] ) && $_POST['title'] != 'notitle' ) ? $_POST['title'] : '';
                                $items_per_page = 5;
                                
                                $sample   = new sample_own_method();
                                $dmvideos = $sample->get_sample_videoListForSideVideos( 'me', $fields = array(
                                                 'id',
                                                'title',
                                                'embed_url',
                                                'thumbnail_url',
                                                'description',
                                                'views_total',
                                                'tags',
                                                'channel.name',
                                                'created_time',
                                                'duration',
                                                'owner.screenname' 
                                ), (int) $pn, $items_per_page, $search_title );
                                header( 'Content-type: application/assets/json' );
                                print json_encode( $dmvideos );
                                exit;
                }
                
                
                //Callback method for My Sample video iframe Short code
                public function sp_dmvideo_func( $atts )
                {
                                global $user_meta;
                                global $pub_option_name;
                                $publisher_settings = $pub_option_name;
                                $publisher_id       = ( isset( $publisher_settings[0]['publisher_id'] ) ) ? $publisher_settings[0]['publisher_id'] : null;
                                $parameter         = ( isset( $publisher_id ) ) ? "?syndication=$publisher_id" : null;
                                extract( $atts );
                                $video_id  = $id;
                                $media_url = $media_url;
                                $width    = ( isset( $width ) ) ? $width : 300;
                                $height   = ( isset( $height ) ) ? $height : 250;
                                $html     = '';
                                if ( isset( $media_url ) && strlen( $video_id ) < 10 ) {
                                                $html .= '<iframe width="' . $width . '" height="' . $height . '" frameborder="0" scrolling="no" src="' . $media_url . '' . $parameter . '"></iframe>';
                                } elseif ( isset( $media_url ) && strlen( $video_id ) > 10 ) {
                                                $html .= '<iframe width="' . $width . '" height="' . $height . '" frameborder="0" scrolling="no" src="' . $media_url . '"></iframe>';
                                }
                                return $html;
                }
}