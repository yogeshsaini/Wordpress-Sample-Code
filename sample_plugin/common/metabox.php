<?php

/**
 * To show metabox at side bar of post add page.This is for both Sample and Sample Cloud APIs.
 * @package    wordpress
 * @author     Sample
 */
class MetaBoxPage
{
  protected $dcuname;
  protected $dcpass;

  var $version = '1.2.3';
  //Initializing methods on creating their objects
  public function __construct($dcuname = null, $dcpass = null)
  {
    $this->dcuname = $dcuname;
    $this->dcpass  = $dcpass;
    global $wp_version;
    add_action('admin_menu', array(
      $this,
      'add_meta_box'
    ));

    //Menu to get Sample Cloud videos
    add_action('wp_ajax_get_my_dmc_video_metabox_tab_videos', array(
      $this,
      'getMyDmcVideoMetaboxTabVideos'
    ));
    //Menu to get Sample My videos
    add_action('wp_ajax_get_my_dm_video_metabox_tab_videos', array(
      $this,
      'getMyDmVideoMetaboxTabVideos'
    ));
    add_action('wp_ajax_check_both_connection', array(
      $this,
      'checkBothConnection'
    ));
    add_shortcode('dmvideo', array(
      $this,
      'dmvideo_func'
    ));
		add_action( 'wpuf_add_post_form_tags', array(
      $this, 'metabox_callback' ), 10, 'top');
  }

  //Callback for wp_ajax_check_both_connection
  public function checkBothConnection()
  {
		global $user_meta;
		global $dm_session_store;
    $dm  = $dm_session_store;
    $dmc = $user_meta;
    if (!empty($dm) && !empty($dmc)) {
      $auth = 'BOTH_CONNECTED';
    } else if (empty($dm) && !empty($dmc)) {
      $auth = 'ONLY_DMC_CONNECTED';
    } else if (!empty($dm) && empty($dmc)) {
      $auth = 'ONLY_DM_CONNECTED';
    } else if (empty($dm) && empty($dmc)) {
      $auth = 'BOTH_DISCONNECTED';
    }
    echo $auth;
    die;
  }

  //Callback method for Sample video gallery page handler

  public function add_meta_box()
  {
    add_meta_box('sample_plugin_metabox', __('Sample Plug-in', 'customgallery'), array(
      $this,
      'metabox_callback'
    ), 'post', 'side', 'high');
  }

  //Callback Method for Sample video detail page menu
  public function metabox_callback()
  {
    require_once(SAMPLE_DIR . '/common/metabox-handler.php');
  }

  //Callback method for get_my_dmc_video_metabox_tab_videos
  public function getMyDmcVideoMetaboxTabVideos()
  {
    $pn                = (isset($_POST['pagenumber'])) ? $_POST['pagenumber'] : 1;
    $searchTitle       = (isset($_POST['title']) && $_POST['title'] != 'notitle') ? $_POST['title'] : '';
    $itemsPerPage      = 5;
    $sample_cloud = new SampleCloudOwnMethod($this->dcuname, $this->dcpass);
    $dmcvideos         = $sample_cloud->getSampleCloudVideos((int) $pn, $itemsPerPage, $searchTitle);
    print json_encode($dmcvideos);
    exit;
  }

  //Callback method for get_my_dm_video_metabox_tab_videos
  public function getMyDmVideoMetaboxTabVideos()
  {
    $pn           = (isset($_POST['pagenumber'])) ? $_POST['pagenumber'] : 1;
    $searchTitle  = (isset($_POST['title']) && $_POST['title'] != 'notitle') ? $_POST['title'] : '';
    $itemsPerPage = 5;

    $sample = new SampleOwnMethod();
    $dmvideos    = $sample->getSampleVideoListForSideVideos('me', $fields = array(
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
    ), (int) $pn, $itemsPerPage, $searchTitle);
    header('Content-type: application/json');
    print json_encode($dmvideos);
    exit;
  }


	//Callback method for My Sample video iframe Short code
  public function dmvideo_func($atts)
  {
		global $user_meta;
		global $pub_option_name;
    $publisherSettings = $pub_option_name;
    $publisherId       = (isset($publisherSettings[0]['publisher_id'])) ? $publisherSettings[0]['publisher_id'] : null;
    $parameter         = (isset($publisherId)) ? "?syndication=$publisherId" : null;
    extract($atts);
    $videoId     = $id;
    $mediaUrl    = $media_url;
    $width       = (isset($width)) ? $width : 300;
    $height      = (isset($height)) ? $height : 250;
    //$credentsDM  = get_option('sample_session_store');
    //$credentsDMC = $user_meta;
    $html        = '';
    //if (isset($mediaUrl) && !empty($credentsDM) && strlen($videoId) < 10) {
    //    $html .= '<iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" src="'.$mediaUrl.''.$parameter.'"></iframe>';
    //} elseif (isset($mediaUrl) && !empty($credentsDMC) && strlen($videoId) > 10) {
    //    $html .= '<iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" src="'.$mediaUrl.'"></iframe>';
    //} elseif (isset($mediaUrl) && empty($credentsDM) && strlen($videoId) < 10) {
    //    $html .= '<h2>Please connect your sample account.</h2>';
    //} elseif (isset($mediaUrl) && empty($credentsDMC) && strlen($videoId) > 10) {
    //    $html .= '<h2>Please connect your sample cloud account.</h2>';
    //}

    if (isset($mediaUrl) && strlen($videoId) < 10) {
      $html .= '<iframe width="' . $width . '" height="' . $height . '" frameborder="0" scrolling="no" src="' . $mediaUrl . '' . $parameter . '"></iframe>';
    } elseif (isset($mediaUrl) && strlen($videoId) > 10) {
      $html .= '<iframe width="' . $width . '" height="' . $height . '" frameborder="0" scrolling="no" src="' . $mediaUrl . '"></iframe>';
    }
    return $html;
  }
}
