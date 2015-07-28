<?php
/**
 * Plugin Name:Sample Plugin
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: This is used to show videos.
 * Version: 2.5.9
 * Author: Daffodil Software Ltd.
 * Author URI: http://URI_Of_The_Plugin_Author
 * License: GPL2
 */

define('SAMPLE_BASENAME', basename(dirname(__FILE__)));
define('SAMPLE_DIR', WP_CONTENT_DIR . '/plugins/' . SAMPLE_BASENAME);
define('SAMPLE_URL', WP_CONTENT_URL . '/plugins/' . SAMPLE_BASENAME);
define('SAMPLE_ADMIN_URL', get_admin_url());
//Include user file
require_once(ABSPATH . "wp-includes/pluggable.php");


class DMCFrontLogin
{

  public function __construct()
  {
    //@register_activation_hook(__FILE__, array( $this, 'activate' ));
    @register_deactivation_hook(__FILE__, array( $this, 'deactivate' ));
    @register_uninstall_hook(__FILE__, array( $this, 'uninstall'));
  }

  public function activate()
  {

  }

  public function deactivate()
  {
    if (!current_user_can('activate_plugins')) {
      return;
    }
    $plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
    check_admin_referer("deactivate-plugin_{$plugin}");
    delete_option('dm_cloud_option_name');
    delete_option('sample_session_store');
    delete_option('publish_id_option_name');
    delete_option('sample_option_auth');

    $meta_type  = 'user';
    $user_id    = 0;
    $meta_key   = 'dmcloud_api_secret';
    $meta_value = '';
    $delete_all = true;
    delete_metadata( $meta_type, $user_id, $meta_key, $meta_value, $delete_all );
    $meta_key_session   = 'sample_session_store';
    delete_metadata( $meta_type, $user_id, $meta_key_session, $meta_value, $delete_all );
    $meta_key_token   = 'sample_option_auth';
    delete_metadata( $meta_type, $user_id, $meta_key_token, $meta_value, $delete_all );
  }

  public function uninstall()
  {

  }
}

$dmcfrontlogin      = new DMCFrontLogin();
$current_user       = wp_get_current_user();
$user_meta          = get_user_meta($current_user->ID, 'dmcloud_api_secret', false);
$dm_option_auth     = get_user_meta($current_user->ID, 'sample_option_auth', false);
$dm_session_store   = get_user_meta($current_user->ID, 'sample_session_store', false);
$pub_option_name    = get_user_meta($current_user->ID, 'publish_id_option_name', false);

require_once SAMPLE_DIR . '/file_config.php';

$dm_settings_page   = new DMSettingsPage();
$MyvideoGalleryPage = new MyVideoGalleryPage(@$user_meta[0]['cloud_user_id_number'], @$user_meta[0]['cloud_api_key']);
$DMvideoGalleryPage = new SampleVideoGalleryPage(@$user_meta[0]['cloud_user_id_number'], @$user_meta[0]['cloud_api_key']);
$metabox            = new MetaBoxPage(@$user_meta[0]['cloud_user_id_number'], @$user_meta[0]['cloud_api_key']);
$upload             = new VideoUpload(@$user_meta[0]['cloud_user_id_number'], @$user_meta[0]['cloud_api_key']);
//$dmc_front_auth     = new DmcFrontOuthForm();