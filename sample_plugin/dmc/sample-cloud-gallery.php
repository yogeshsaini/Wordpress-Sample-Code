<?php
/**
 * Dalilymotion cloud
 * @package    wordpress
 * @author     Sample
 */
class My_Video_Gallery_Page
{
                const ITEMS_PER_PAGE = 6; //Number of rows on page
                protected $dcuname;
                protected $dcpass;
                
                /**
                 * Initializing methods on creating their objects
                 *
                 */
                public function __construct( $dcuname = null, $dcpass = null )
                {
                                $this->dcuname = $dcuname;
                                $this->dcpass  = $dcpass;
                                add_action( 'admin_menu', array(
                                                 $this,
                                                'sp_add_plugin_page' 
                                ) );
                                add_action( 'wp_ajax_delete_dm_cloud_records', array(
                                                 $this,
                                                'sp_delete_dm_cloud_records_callback' 
                                ) );
                                add_action( 'wp_ajax_sp_edit_dm_cloud_records', array(
                                                 $this,
                                                'sp_edit_dm_cloud_records' 
                                ) );
                                add_action( 'wp_ajax_sp_delete_dm_cloud_metatags', array(
                                                 $this,
                                                'sp_delete_dm_cloud_metatags' 
                                ) );
                                add_action( 'wp_ajax_sp_update_dm_cloud_metatags', array(
                                                 $this,
                                                'sp_update_dm_cloud_metatags' 
                                ) );
                                add_action( 'wp_ajax_action_upload_pattern', array(
                                                 $this,
                                                'sp_sample_cloud_upload_pattern' 
                                ) );
                                add_action( 'wp_ajax_samplecloud_change_gallery_records', array(
                                                 $this,
                                                'sp_get_sample_cloud_videos' 
                                ) );
                }
                
                /**
                 * Add options page
                 *
                 */
                public function sp_add_plugin_page( )
                {
                                //This page will be under "Settings"
                                add_submenu_page( 'dm-admin-setting', 'Video Gallery', 'Gallery', 'read', 'video-gallery-page', array(
                                                 $this,
                                                'sp_video_gallery_page' 
                                ) );
                }
                
                /**
                 * Callback Method for Sample cloud video gallery page
                 *
                 */
                public function sp_video_gallery_page( )
                {
                                require_once SAMPLE_DIR . '/dmc/dm-cloud-video-gallery-html.php';
                }
                
                /**
                 * Callback Method for Sample cloud video gallery page
                 *
                 */
                public function sp_get_sample_cloud_videos( $search_title = '', $sortby = '-created' )
                {
                                $samplecloud           = new Sample_Cloud_OwnMethod( $this->dcuname, $this->dcpass );
                                $pn                    = ( isset( $_GET['pagenum'] ) ) ? preg_replace( '#[^0-9]#i', '', $_GET['pagenum'] ) : 1;
                                $items_per_page          = (int) self::ITEMS_PER_PAGE;
                                $return_dmcloud_videos = $samplecloud->sp_get_sample_cloud_videos( (int) $pn, $items_per_page, $search_title, $sortby );
                                return $return_dmcloud_videos;
                }
                
                /**
                 * Callback Method for Sample cloud video gallery page
                 *
                 */
                public function sp_get_dmc_keywords( $mediaId )
                {
                                $samplecloud = new Sample_Cloud_OwnMethod( $this->dcuname, $this->dcpass );
                                return $samplecloud->sp_get_sample_cloud_videosKeywords( $mediaId );
                }
                
                /**
                 * Method to delete Sample cloud video by media id
                 *
                 */
                public function sp_delete_dm_cloud_records_callback( )
                {
                                $media_id = ( $_POST['mediaId'] ) ? $_POST['mediaId'] : null;
                                if ( !empty( $media_id ) ) {
                                                $samplecloud = new Sample_Cloud_OwnMethod( $this->dcuname, $this->dcpass );
                                                $delete      = ( $samplecloud->delete_sample_cloud_media( $media_id ) ) ? true : false;
                                                session_start();
                                                if ( !isset( $_SESSION['dmc_success'] ) || $_SESSION['dmc_success'] == "" ) {
                                                                $_SESSION['dmc_success'] = 'Your video has been deleted.';
                                                }
                                }
                }
                
                /**
                 * Method to edit Sample cloud video by media Id
                 *
                 */
                public function sp_edit_dm_cloud_records( )
                {
                                $media_id = ( $_POST['mediaId'] ) ? $_POST['mediaId'] : null;
                                if ( !empty( $media_id ) ) {
                                                $samplecloud   = new Sample_Cloud_OwnMethod( $this->dcuname, $this->dcpass );
                                                $video_info     = $samplecloud->sp_get_sample_cloud_videosDetails( $media_id );
                                                $playerList    = $samplecloud->get_sample_cloud_player();
                                                $media_image_url = !empty( $video_info['stream_url'] ) ? $video_info['stream_url'] : SAMPLE_URL . '/assets/img/no_files_found.jpg';
                                                $curpage       = !empty( $_POST['curpage'] ) ? $_POST['curpage'] : 'notfound';
                                                $str           = '';
                                                $str .= '<div class="dmc-edit-container dm-common">';
                                                $str .= '<div class="logo">
            <h2>Edit Video</h2>
            <span class="logo"></span>
            </div>';
                                                $str .= '<script type="text/javascript" src="' . SAMPLE_URL . '/assets/js/ajax-upload_pattern.js"></script>';
                                                $str .= '<form enctype="multipart/form-data" action="" id="dm_sp_update_form" method="post">';
                                                $str .= '<input type="hidden" id="counter-value" value="1" />';
                                                $str .= '<input type="hidden" id="curpage" name="curpage" value="' . $curpage . '" />';
                                                $str .= '<input type="hidden" name="media_id" size="50" value="' . $video_info['media_id'] . '" />';
                                                $str .= '<div class="top-row">
            <div class="label">Thumbnail:</div>
            <div class="thumbnail">
               <div class="thumb-img"><img class="edit-video-thumbnail" src="' . $media_image_url . '" alt="" /></div>
               <div class="thumb-right">
              <a id="browse_file" href="#">Change Thumbnail</a>
              <input type="hidden" id="attach_id" name="at_id" value="" />
              <input type="hidden" id="attach_url" name="attach_url" value="" />
              <div class="msg">
                 <p>Minimum 150 px wide</p>
                 <p>Recommended aspect ratio: 4:3 or 16:9</p>
              </div>
               </div>
            </div>
             </div>';
                                                $str .= '<div class="middle-row">';
                                                $str .= '<div class="head">Custom Tags</div>';
                                                $str .= '<div class="middle-wrapper">';
                                                $str .= '<div class="title"><label>Video Title:</label><input type="text" name="title" id="video-title" value="' . $video_info['meta']['title'] . '" /></div>';
                                                $str .= '<div class="present-tags">';
                                                if ( !empty( $video_info['meta'] ) ) {
                                                                $metatags = $video_info['meta'];
                                                                $i        = 1;
                                                                foreach ( $metatags as $key => $val ) {
                                                                                if ( $key != 'title' ) {
                                                                                                $str .= '<div class="tag" id="meta_' . $key . '">
            <label>' . $key . '</label>
            <input type="hidden" size="50" class="keyInput" name="originalmeta[' . $key . '][]" value="' . $key . '" />
            <input type="text" name="originalmeta[' . $key . '][]" value="' . $val . '" />
            <a class="delete-tag" onclick="deleteMetatags(\'' . $video_info['media_id'] . '\',\'' . $key . '\');" href="javascript:void(0);">Remove</a>
             </div>';
                                                                                }
                                                                                $i++;
                                                                }
                                                }
                                                $str .= '</div>';
                                                $str .= '<div class="new-tags">';
                                                $str .= '<div class="tag"><input type="text" size="50" class="keyInput" name="meta[val1][]" placeholder="Name"><input size="50" type="text" name="meta[val1][]" placeholder="Value"><a href="javascript:void(0);" id="dmc-new-tag">Add</a></div>';
                                                $str .= '</div>';
                                                $str .= '</div>';
                                                $str .= '</div>';
                                                $str .= '<div class="bottom-row">';
                                                $str .= '<div class="preview">
        <label>Preview</label>
        <div class="iframe"><iframe width="338" height="150" frameborder="0" scrolling="no" src="' . $video_info['embed_url'] . '"></iframe></div>
         </div>';
                                                $str .= '</div>';
                                                $str .= '<div class="alert-msg" id="dmc-message"></div>';
                                                $str .= '<div class="footer-row">
            <div class="delete">
               <a href="javascript:void(0);" class="dmc-trash-trigger">Delete this video</a>
               <div class="confirm-box">
              <div class="head"><span class="arrow"></span>Delete this video?</div>
              <div class="message">This video will be deleted from your Sample Cloud account.</div>
              <a class="dmc-keep-it" href="javascript:void(0);">No, keep it</a>
              <a rel="' . $media_id . '" class="dmc-delete-it" href="javascript:void(0);">Yes, delete</a>
               </div>
            </div>
            <div class="save">
               <a class="save_new_data" onclick="return getDMCsp_updatedvalues();" href="javascript:void(0);">Save</a>
            </div>
             </div>
           </div>';
                                                $str .= '</form>';
                                                $str .= '</div>';
                                                echo $str;
                                                exit( );
                                }
                }
                
                /**
                 * Method calling from ajax to delete the Sample cloud video meta tags
                 *
                 */
                public function sp_delete_dm_cloud_metatags( )
                {
                                $mediaId = ( $_POST['mediaId'] ) ? $_POST['mediaId'] : null;
                                $key     = ( $_POST['key'] ) ? $_POST['key'] : null;
                                if ( !empty( $mediaId ) && !empty( $key ) ) {
                                                $samplecloud = new Sample_Cloud_OwnMethod( $this->dcuname, $this->dcpass );
                                                $samplecloud->remove_sample_cloud_video_metas( $mediaId, array(
                                                                 $key 
                                                ) );
                                }
                }
                
                /**
                 * Method to sp_update Sample cloud Meta tags
                 *
                 */
                public function sp_update_dm_cloud_metatags( )
                {
                                $meta             = array( );
                                $originalmetameta = array( );
                                $mediaId          = !empty( $_POST['media_id'] ) ? $_POST['media_id'] : null;
                                $imageurl         = !empty( $_POST['attach_url'] ) ? $_POST['attach_url'] : null;
                                $imageid          = !empty( $_POST['at_id'] ) ? $_POST['at_id'] : null;
                                if ( $mediaId ) {
                                                $samplecloud   = new Sample_Cloud_OwnMethod( $this->dcuname, $this->dcpass );
                                                $meta['title'] = !empty( $_POST['title'] ) ? $_POST['title'] : null;
                                                $sp_updatemeta    = !empty( $_POST['originalmeta'] ) ? array_keys( $_POST['originalmeta'] ) : null;
                                                if ( !empty( $_POST['meta'] ) ) {
                                                                foreach ( $_POST['meta'] as $key => $data ) {
                                                                                if ( !empty( $data[0] ) && !empty( $data[1] ) ) {
                                                                                                $meta[$data[0]] = (string) $data[1];
                                                                                }
                                                                }
                                                }
                                                if ( !empty( $_POST['originalmeta'] ) ) {
                                                                foreach ( $_POST['originalmeta'] as $originalmetakey => $originalmetadata ) {
                                                                                if ( !empty( $originalmetadata[0] ) && !empty( $originalmetadata[1] ) ) {
                                                                                                $originalmetameta[$originalmetadata[0]] = (string) $originalmetadata[1];
                                                                                }
                                                                }
                                                }
                                                if ( $imageurl ) {
                                                                $samplecloud->set_sample_cloud_video_thumbnail( $mediaId, $imageurl );
                                                }
                                                if ( $sp_updatemeta ) {
                                                                $samplecloud->remove_sample_cloud_video_metas( $mediaId, $sp_updatemeta );
                                                                $samplecloud->set_sample_cloud_video_metas( $mediaId, $originalmetameta );
                                                }
                                                if ( $meta ) {
                                                                $samplecloud->set_sample_cloud_video_metas( $mediaId, $meta );
                                                }
                                                session_start();
                                                if ( !isset( $_SESSION['dmc_success'] ) || $_SESSION['dmc_success'] == "" ) {
                                                                $_SESSION['dmc_success'] = 'Your video was saved successfully.';
                                                }
                                                print json_encode( array(
                                                                 'msg' => 'Succesfuly sp_update data' 
                                                ) );
                                                exit;
                                }
                }
                
                /**
                 * Method to upload thumbnail image
                 *
                 */
                public function sp_sample_cloud_upload_pattern( )
                {
                                if ( !function_exists( 'wp_handle_upload' ) )
                                                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                                if ( $_POST['attachment_id'] )
                                                wp_delete_attachment( $_POST['attachment_id'], true );
                                $uploadedfile     = $_FILES['file'];
                                $upload_overrides = array(
                                                 'test_form' => false 
                                );
                                $movefile         = wp_handle_upload( $uploadedfile, $upload_overrides );
                                if ( isset( $movefile['file'] ) ) {
                                                $file_loc    = $movefile['file'];
                                                $file_name   = basename( $movefile['file'] );
                                                $file_type   = wp_check_filetype( $file_name );
                                                $attachment  = array(
                                                                 'post_mime_type' => $file_type['type'],
                                                                'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
                                                                'post_content' => '',
                                                                'post_status' => 'inherit' 
                                                );
                                                $attach_id   = wp_insert_attachment( $attachment, $file_loc );
                                                $attach_data = wp_generate_attachment_metadata( $attach_id, $file_loc );
                                                wp_sp_update_attachment_metadata( $attach_id, $attach_data );
                                                $return = array(
                                                                 'data' => $attach_data,
                                                                'id' => $attach_id,
                                                                'url' => wp_get_attachment_url( $attach_id ) 
                                                );
                                                print json_encode( $return );
                                } else {
                                                print 'Something goes wrong';
                                }
                                exit;
                }
}