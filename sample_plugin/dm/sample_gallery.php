<?php
/**
 * Dalilymotion
 * @package    wordpress
 * @author     Sample
 */
class SampleVideoGalleryPage
{
    const ITEMS_PER_PAGE = 6; //Number of columns on page
    protected $dcuname;
    protected $dcpass;

    /**
     * Dalilymotion
     */
    public function __construct($dcuname = null, $dcpass = null)
    {
        $this->dcuname = $dcuname;
        $this->dcpass  = $dcpass;
        add_action('wp_ajax_change_gallery_records', array(
            $this,
            'getSampleVideos'
        ));
        add_action('admin_menu', array(
            $this,
            'sampleVideoGalleryPageHandler'
        ));
        //add_action('admin_menu', array(
        //    $this,
        //    'sampleVideoDetailPage'
        //));
        add_action('admin_menu', array(
            $this,
            'sampleSearchResultPage'
        ));
        add_action('wp_ajax_delete_dm_video', array(
            $this,
            'deleteSampleVideoCallback'
        ));
        add_action('wp_ajax_edit_sample_records', array(
            $this,
            'editSampleRecords'
        ));
        add_action('wp_ajax_update_sample_datas', array(
            $this,
            'updateSampleRecords'
        ));
        //add_action( 'admin_init',  array(
        //    $this,
        //    'wpse_60168_var_dump_and_die'
        //));
    }

    //public function wpse_60168_var_dump_and_die()
    //{
    //    global $menu;
    //    echo '<pre>' . print_r( $menu, true ) . '</pre>';
    //    wp_die();
    //}

    /**
     * Callback method for Sample video gallery page handler.
     */
    public function sampleVideoGalleryPageHandler()
    {
        add_submenu_page('dm-admin-setting', 'Sample video gallery', 'Sample video gallery', 'read', 'dm-video-gallery', array(
            $this,
            'sampleVideoGalleryPageCallback'
        ));
    }

    /**
     * Callback Method for Sample video detail page menu
     */
    //public function sampleVideoDetailPage()
    //{
    //    add_submenu_page(null, 'Sample video', 'Sample video', 'administrator', 'dm-video-detail', array(
    //        $this,
    //        'sampleVideoDetailPageCallback'
    //    ));
    //}

    /**
     * Callback Method for Sample video detail page menu
     */
    public function sampleSearchResultPage()
    {
        add_submenu_page(null, 'Sample search result', 'Sample search result', 'administrator', 'dm-search-result', array(
            $this,
            'sampleSearchResultPageCallback'
        ));
    }

    /**
     * Callback Method for Sample Search
     */
    public function sampleSearchResultPageCallback()
    {
        if (isset($_POST['sample_video_title'])) {
            require_once SAMPLE_DIR . '/dm/dm-search-result.php';
        } else {
            echo '<div class="no-title">Please select title to search.</div>';
        }
    }

    /**
     * Callback method for Sample video detail page
     */
    //public function sampleVideoDetailPageCallback()
    //{
    //    require_once SAMPLE_DIR . '/dm/dm-video-detail.php';
    //}

    /**
     * Callback method for Daily motion video gallery page
     */
    public function sampleVideoGalleryPageCallback()
    {
        require_once SAMPLE_DIR . '/dm/dm-video-gallery-html.php';
    }

    /**
     * Method to display Sample vodeo gallery
     */
    public function getSampleVideos($selected = 'me', $search_title, $status = 'all')
    {
        $sample  = new SampleOwnMethod();
        $pn           = (isset($_GET['pageno'])) ? preg_replace('#[^0-9]#i', '', $_GET['pageno']) : 1;
        $itemsPerPage = (int) self::ITEMS_PER_PAGE;
        $dmvideos     = $sample->getSampleVideoList($selected, $fields = array('id', 'title',
        'embed_url', 'thumbnail_url', 'description', 'views_total', 'tags', 'channel.name', 'created_time', 'duration', 'private', 'published'
        ), $status, (int) $pn, $itemsPerPage, $search_title);
        return $dmvideos;
    }

    /**
     * Method to display My Sample video Preview
     */
    public function playMyDMVideoiFrame($title = null, $embed_url = null)
    {
        if (!empty($title) && !empty($embed_url)) {
            $str = '';
            $str .= '<div class="video-container">';
            $str .= '<h2>' . $title . '</h2>';
            if (!empty($embed_url)) {
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
    public function deleteSampleVideoCallback()
    {
        $Id          = (isset($_POST['Id'])) ? $_POST['Id'] : null;
        $sample = new SampleOwnMethod();
        $sample->deleteSampleVideo($Id);
        session_start();
        if(!isset($_SESSION['dm_success']) || $_SESSION['dm_success'] == "")
        {
            $_SESSION['dm_success'] = 'Your video has been deleted.';
        }
    }

    /**
     * Method to edit Sample video by media Id
     */
    public function editSampleRecords()
    {
        $media_id = ($_POST['mediaId']) ? $_POST['mediaId'] : null;
        if (!empty($media_id)) {
            $sample   = new SampleOwnMethod();
            $videoInfo     = $sample->getSampleVideoDetail($media_id);
            $chennelslist  = $sample->getSampleChannelList();
            $mediaImageURL = !empty($videoInfo['thumbnail_url']) ? $videoInfo['thumbnail_url'] : SAMPLE_URL . '/img/no_files_found.jpg';
            $description   = !empty($videoInfo['description']) ? $videoInfo['description'] : '';
            $tags          = !empty($videoInfo['tags']) ? implode(', ', $videoInfo['tags']) : '';
            $Channels      = !empty($videoInfo['channel']) ? $videoInfo['channel'] : '';
            $curpage       = !empty($_POST['curpage']) ? $_POST['curpage'] : 'notfound';
            $str           = '';
            $str .= '<script type="text/javascript" src="' . SAMPLE_URL . '/js/ajax-upload_pattern.js"></script>';
            $str .= '<div class="dmc-edit-container dm-common">';
            $str .= '<form enctype="multipart/form-data" action="" id="dm_update_form" method="post">';
            $str .= '<input type="hidden" name="id" size="50" value="' . $videoInfo['id'] . '" />';
            $str .= '<input type="hidden" id="curpage" name="curpage" value="'.$curpage.'" />';
            $str .= '<input type="hidden" id="status_publish" name="data[published]" value="true" />';
            $str .= '<div class="logo">
                        <h2>Edit Video</h2>
                        <span class="logo"></span>
                    </div>';
            $str .= '<div class="title-wrap">
                        <label><span class="required">*</span>Video Title:</label>
                        <input type="text" name="data[title]" id="video-title" value="' . $videoInfo['title'] . '" />
                    </div>';
            $str .= '<div class="desc-wrap">
                        <label>Video Description:</label>
                        <textarea  name="data[description]" id="video-title">' . $description . '</textarea>
                     </div>';
            $str .= '<div class="channel-wrap">
                     <label><span class="required">*</span>Channel: <span class="qus_mark tooltip"><span><img class="callout" src="'.SAMPLE_URL . '/img/callout.gif" />Sample Publisher allows you to earn advertising revenue when sharing Sample videos on your site.</span></span></label>
                     <select type="text" id="channel" name="data[channel]">';
            $str .= '<option value="">Please select</option>';
            if (isset($chennelslist) && !empty($chennelslist)) {
                foreach ($chennelslist as $ck => $cv) {
                    (isset($Channels) && ($Channels == $ck)) ? $selected = 'selected="selected"' : $selected = '';
                    $str .= '<option value="' . $ck . '" ' . $selected . '>' . $cv . '</option>';
                }
            }
            $str .= '</select></div>';
            $change = '';
            if (isset($videoInfo['type']) && $videoInfo['type'] == 'official'):
                $change = '<a id="browse_file" href="#">Change Thumbnail</a>
                     <input type="hidden" id="attach_id" name="at_id" value="" />
                     <input type="hidden" id="attach_url" name="data[thumbnail_url]" value="" />';
            endif;
            $str .= '<div class="thumb-wrap">
                     <label>Thumbnail:</label>
                     <div class="video-thumb">
                        <img class="edit-video-thumbnail" src="' . $mediaImageURL . '" alt="" width="150" height="150"/>
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
            $checked_private = (isset($videoInfo['private']) && $videoInfo['private'] == 1) ? 'checked="checked"' : '';
            $checked_public  = (isset($videoInfo['private']) && $videoInfo['private'] == 1) ? '' : 'checked="checked"';

            $class_private = (isset($videoInfo['private']) && $videoInfo['private'] == 1) ? 'blur' : '';
            $class_public  = (isset($videoInfo['private']) && $videoInfo['private'] == 1) ? '' : 'blur';

            $str .= '<div class="visibility-wrap visibility_private '.$class_public.'">
                        <label>Visibility:</label>
                        <label><input id="dm-video-private" type="radio" name="dm_video_status" value="1" name="data[private]" ' . $checked_private . '>Private
                        <div class="cls_visibility">
                           <p>This video is private and accessable through private link below</p>
                           <span>Private URL</soan>
                        </div></label>
                  </div>';
            $str .= '<div class="visibility-wrap visibility_public '.$class_private.'">
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
                                   <a class="save_new_data" onclick="return getSampleupdatedvalues();" href="javascript:void(0);">Save</a>
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
     * Method to update sample video
     */
    public function updateSampleRecords()
    {
        if (isset($_POST['id']) && !empty($_POST['id']) && is_array($_POST['data'])) {
            $sample = new SampleOwnMethod();
            $videoInfo   = $sample->updateSampleVideoData($_POST['id'], array_filter($_POST['data']));
            session_start();
            if(!isset($_SESSION['dm_success']) || $_SESSION['dm_success'] == "")
            {
                $_SESSION['dm_success'] = 'Your video was saved successfully.';
            }
            print json_encode(array('msg' => 'Successfully updated'));
            exit;
        }
    }
}
