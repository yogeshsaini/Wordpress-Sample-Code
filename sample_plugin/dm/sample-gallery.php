<?php
/**
 * Dalilymotion
 * @package    wordpress
 * @author     Sample
 */
class Sample_Video_Gallery_Page
{
                const ITEMS_PER_PAGE = 6; //Number of columns on page
                protected $dcuname;
                protected $dcpass;
                
                /**
                 * Dalilymotion
                 */
                public function __construct( $dcuname = null, $dcpass = null )
                {
                                $this->dcuname = $dcuname;
                                $this->dcpass  = $dcpass;
                                add_action( 'wp_ajax_change_gallery_records', array(
                                                 $this,
                                                'sp_get_sample_videos' 
                                ) );
                                add_action( 'admin_menu', array(
                                                 $this,
                                                'sp_sample_sp_video_gallery_page_handler' 
                                ) );
                                //add_action('admin_menu', array(
                                //    $this,
                                //    'sampleVideoDetailPage'
                                //));
                                add_action( 'admin_menu', array(
                                                 $this,
                                                'sp_sample_search_result_page' 
                                ) );
                                add_action( 'wp_ajax_delete_dm_video', array(
                                                 $this,
                                                'sp_delete_sample_video_callback' 
                                ) );
                                add_action( 'wp_ajax_sp_edit_sample_records', array(
                                                 $this,
                                                'sp_edit_sample_records' 
                                ) );
                                add_action( 'wp_ajax_sp_update_sample_datas', array(
                                                 $this,
                                                'sp_update_sample_records' 
                                ) );
                }
                /**
                 * Callback method for Sample video gallery page handler.
                 */
                public function sp_sample_sp_video_gallery_page_handler( )
                {
                                add_submenu_page( 'dm-admin-setting', 'Sample video gallery', 'Sample video gallery', 'read', 'dm-video-gallery', array(
                                                 $this,
                                                'sp_sample_sp_video_gallery_page_callback' 
                                ) );
                }
                
                /**
                 * Callback Method for Sample video detail page menu
                 */
                public function sp_sample_search_result_page( )
                {
                                add_submenu_page( null, 'Sample search result', 'Sample search result', 'administrator', 'dm-search-result', array(
                                                 $this,
                                                'sp_sample_search_result_pageCallback' 
                                ) );
                }
                
                /**
                 * Callback Method for Sample Search
                 */
                public function sp_sample_search_result_pageCallback( )
                {
                                if ( isset( $_POST['sample_video_title'] ) ) {
                                                require_once SAMPLE_DIR . '/dm/dm-search-result.php';
                                } else {
                                                echo '<div class="no-title">Please select title to search.</div>';
                                }
                }
                /**
                 * Callback method for Daily motion video gallery page
                 */
                public function sp_sample_sp_video_gallery_page_callback( )
                {
                                require_once SAMPLE_DIR . '/dm/dm-video-gallery-html.php';
                }
                
                /**
                 * Method to display Sample vodeo gallery
                 */
                public function sp_get_sample_videos( $selected = 'me', $search_title, $status = 'all' )
                {
                                $sample       = new sample_own_method();
                                $pn           = ( isset( $_GET['pageno'] ) ) ? preg_replace( '#[^0-9]#i', '', $_GET['pageno'] ) : 1;
                                $items_per_page = (int) self::ITEMS_PER_PAGE;
                                $dmvideos     = $sample->get_sample_videoList( $selected, $fields = array(
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
                                                'private',
                                                'published' 
                                ), $status, (int) $pn, $items_per_page, $search_title );
                                return $dmvideos;
                }
                
                /**
                 * Method to display My Sample video Preview
                 */
                public function sp_play_my_dm_video_iframe( $title = null, $embed_url = null )
                {
                                if ( !empty( $title ) && !empty( $embed_url ) ) {
                                                $str = '';
                                                $str .= '<div class="video-container">';
                                                $str .= '<h2>' . $title . '</h2>';
                                                if ( !empty( $embed_url ) ) {
                                                                $str .= '<iframe width="384" height="200" frameborder="0" scrolling="no" src="' . $embed_url . '"></iframe>';
                                                } else {
                                                                $str .= "No video to display";
                                                }
                                                $str .= '</div>';
                                                return $str;
                                }
                }
                
                /**
                 * Method to delete sample video
                 */
                public function sp_delete_sample_video_callback( )
                {
                                $Id     = ( isset( $_POST['Id'] ) ) ? $_POST['Id'] : null;
                                $sample = new sample_own_method();
                                $sample->delete_sample_video( $Id );
                                session_start();
                                if ( !isset( $_SESSION['dm_success'] ) || $_SESSION['dm_success'] == "" ) {
                                                $_SESSION['dm_success'] = 'Your video has been deleted.';
                                }
                }
                
                /**
                 * Method to edit Sample video by media Id
                 */
                public function sp_edit_sample_records( )
                {
                                $media_id = ( $_POST['mediaId'] ) ? $_POST['mediaId'] : null;
                                if ( !empty( $media_id ) ) {
                                                $sample        = new sample_own_method();
                                                $video_info     = $sample->get_sample_video_detail( $media_id );
                                                $chennelslist  = $sample->get_sample_channel_list();
                                                $media_image_url = !empty( $video_info['thumbnail_url'] ) ? $video_info['thumbnail_url'] : SAMPLE_URL . '/assets/img/no_files_found.jpg';
                                                $description   = !empty( $video_info['description'] ) ? $video_info['description'] : '';
                                                $tags          = !empty( $video_info['tags'] ) ? implode( ', ', $video_info['tags'] ) : '';
                                                $Channels      = !empty( $video_info['channel'] ) ? $video_info['channel'] : '';
                                                $curpage       = !empty( $_POST['curpage'] ) ? $_POST['curpage'] : 'notfound';
                                                $str           = '';
                                                $str .= '<script type="text/javascript" src="' . SAMPLE_URL . '/assets/js/ajax-upload_pattern.js"></script>';
                                                $str .= '<div class="dmc-edit-container dm-common">';
                                                $str .= '<form enctype="multipart/form-data" action="" id="dm_sp_update_form" method="post">';
                                                $str .= '<input type="hidden" name="id" size="50" value="' . $video_info['id'] . '" />';
                                                $str .= '<input type="hidden" id="curpage" name="curpage" value="' . $curpage . '" />';
                                                $str .= '<input type="hidden" id="status_publish" name="data[published]" value="true" />';
                                                $str .= '<div class="logo">
                        <h2>Edit Video</h2>
                        <span class="logo"></span>
                    </div>';
                                                $str .= '<div class="title-wrap">
                        <label><span class="required">*</span>Video Title:</label>
                        <input type="text" name="data[title]" id="video-title" value="' . $video_info['title'] . '" />
                    </div>';
                                                $str .= '<div class="desc-wrap">
                        <label>Video Description:</label>
                        <textarea  name="data[description]" id="video-title">' . $description . '</textarea>
                     </div>';
                                                $str .= '<div class="channel-wrap">
                     <label><span class="required">*</span>Channel: <span class="qus_mark tooltip"><span><img class="callout" src="' . SAMPLE_URL . '/assets/img/callout.gif" />Sample Publisher allows you to earn advertising revenue when sharing Sample videos on your site.</span></span></label>
                     <select type="text" id="channel" name="data[channel]">';
                                                $str .= '<option value="">Please select</option>';
                                                if ( isset( $chennelslist ) && !empty( $chennelslist ) ) {
                                                                foreach ( $chennelslist as $ck => $cv ) {
                                                                                ( isset( $Channels ) && ( $Channels == $ck ) ) ? $selected = 'selected="selected"' : $selected = '';
                                                                                $str .= '<option value="' . $ck . '" ' . $selected . '>' . $cv . '</option>';
                                                                }
                                                }
                                                $str .= '</select></div>';
                                                $change = '';
                                                if ( isset( $video_info['type'] ) && $video_info['type'] == 'official' ):
                                                                $change = '<a id="browse_file" href="#">Change Thumbnail</a>
                     <input type="hidden" id="attach_id" name="at_id" value="" />
                     <input type="hidden" id="attach_url" name="data[thumbnail_url]" value="" />';
                                                endif;
                                                $str .= '<div class="thumb-wrap">
                     <label>Thumbnail:</label>
                     <div class="video-thumb">
                        <img class="edit-video-thumbnail" src="' . $media_image_url . '" alt="" width="150" height="150"/>
                     </div>
                     <div class="thumb-right">
                        ' . $change . '
                        <div class="msg">
                           <p>Minimum 150 px wide</p>
                           <p>Recommended aspect ratio: 4:3 or 16:9</p>
                        </div>
                     </div>
                  </div>';
                                                $str .= '<div class="tags-wrap">
                        <label>Tag(s):</label>
                        <input type="text" class="tags" name="data[tags]" maxlength="250" size="50" id="video-tags" value="' . $tags . '" />
                    </div>';
                                                $checked_private = ( isset( $video_info['private'] ) && $video_info['private'] == 1 ) ? 'checked="checked"' : '';
                                                $checked_public  = ( isset( $video_info['private'] ) && $video_info['private'] == 1 ) ? '' : 'checked="checked"';
                                                
                                                $class_private = ( isset( $video_info['private'] ) && $video_info['private'] == 1 ) ? 'blur' : '';
                                                $class_public  = ( isset( $video_info['private'] ) && $video_info['private'] == 1 ) ? '' : 'blur';
                                                
                                                $str .= '<div class="visibility-wrap visibility_private ' . $class_public . '">
                        <label>Visibility:</label>
                        <label><input id="dm-video-private" type="radio" name="dm_video_status" value="1" name="data[private]" ' . $checked_private . '>Private
                        <div class="cls_visibility">
                           <p>This video is private and accessable through private link below</p>
                           <span>Private URL</soan>
                        </div></label>
                  </div>';
                                                $str .= '<div class="visibility-wrap visibility_public ' . $class_private . '">
                        <label><input id="dm-video-public" type="radio" name="dm_video_status" value="0" name="data[private]" ' . $checked_public . '>Public
                        <div class="cls_visibility">
                           <p>This video is public and can be seen by anyone on Sample.com.</p>
                           <span>Private URL</span>
                        </div></label>
                  </div>';
                                                $str .= '<div class="alert-msg" id="dm-message"></div>';
                                                $str .= '<div class="footer-row">
                                <div class="delete">
                                   <a href="javascript:void(0);" class="dmc-trash-trigger">Delete this video</a>
                                   <div class="confirm-box">
                                      <div class="head"><span class="arrow"></span>Delete this video?</div>
                                      <div class="message">This video will be deleted from your Sample.com account.</div>
                                      <a class="dmc-keep-it" href="javascript:void(0);">No, keep it</a>
                                      <a rel="' . $media_id . '" class="delete-it" href="javascript:void(0);">Yes, delete</a>
                                   </div>
                                </div>
                                <div class="save">
                                   <a class="save_new_data" onclick="return getSamplesp_updatedvalues();" href="javascript:void(0);">Save</a>
                                </div>
                             </div>
                       </div>';
                                                $str .= '</form>';
                                                $str .= '</div>';
                                                echo $str;
                                                die;
                                }
                }
                
                /**
                 * Method to sp_update sample video
                 */
                public function sp_update_sample_records( )
                {
                                if ( isset( $_POST['id'] ) && !empty( $_POST['id'] ) && is_array( $_POST['data'] ) ) {
                                                $sample    = new sample_own_method();
                                                $video_info = $sample->sp_update_sample_video_data( $_POST['id'], array_filter( $_POST['data'] ) );
                                                session_start();
                                                if ( !isset( $_SESSION['dm_success'] ) || $_SESSION['dm_success'] == "" ) {
                                                                $_SESSION['dm_success'] = 'Your video was saved successfully.';
                                                }
                                                print json_encode( array(
                                                                 'msg' => 'Successfully sp_updated' 
                                                ) );
                                                exit;
                                }
                }
}