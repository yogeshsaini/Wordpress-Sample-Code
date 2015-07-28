<?php
/**
 * DMSettingsPage Class
 *
 * @category Class
 * @package  Sample
 * @author   Olivier Poitrey <rs@sample.com>
 * @license  GNU General Public License
 * @link     http://www.sample.com/
 *
 */
class DMSettingsPage
{
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  private $SampleOwnMethod;

  /**
   * Start up
   */
  public function __construct()
  {
    add_action('admin_menu', array(
      $this,
      'add_plugin_page'
    ));
    add_action('admin_init', array(
      $this,
      'page_init'
    ));
    add_action('admin_enqueue_scripts', array(
      $this,
      'sample_load_js_and_css'
    ));
    add_action('wp_enqueue_scripts', array(
      $this,
      'sample_load_js_and_css'
    ));
    add_action('wp_ajax_update', array(
      $this,
      'update'
    ));
    add_action('wp_ajax_discconet_account', array(
      $this,
      'discconetAccount'
    ));
    add_action('admin_init', array(
      $this,
      'publish_id_setting_init'
    ));
    add_action('admin_init', array(
      $this,
      'sample_setting_init'
    ));
    add_action('user_register', array(
      $this,
      'sample_registration_save'
    ), 50, 1);
    add_filter('wp_login_errors', array(
      $this,
      'override_reg_complete_msg'
    ), 10, 2);
    add_action('login_message', array(
      $this,
      'change_login_message'
    ));
    add_action('wp_ajax_create_dm_cloud_account', array(
            $this,
            'createCloudAccount'
        ));
  }

  /**
   *  create user account on sample cloud when new user register
   */
  public function sample_registration_save($user_id)
  {
    global $wpdb;
    $uid     = $user_id;
    $user_id = '53a17d14947399435432a24f';
    $api_key = '853c339e3f50a3e3ad9443db2bd12315cfde26ad';

    $DmcOwnMethod = new SampleCloudOwnMethod($user_id, $api_key);
    $email        = !empty($_POST['user_email']) ? $_POST['user_email'] : $_POST['email'];
    $user_login   = !empty($_POST['user_login']) ? $_POST['user_login'] : '';
    $returndata   = (array) $DmcOwnMethod->createNewUserOnOrganigation($user_id, $user_login, $email);
    if (!empty($returndata['error']) && (trim($returndata['error']['message']) != trim("Error Msg : $user_login / $email"))) {
      $errors = new WP_Error();
      $errors->add('sample', __($returndata['error']['message']), 'error');
      require_once(ABSPATH . 'wp-admin/includes/user.php');
      if (wp_delete_user($uid)) {
        wp_die($errors->get_error_message(), __('Input Error', 'sample'));
      } else {
        wp_die('User not deleted', __('Input Error', 'sample'));
      }
    }
    $subject = 'Sample cloud Account Detail';
    $headers[] = 'From: '.get_option('blogname').' <'.get_option('admin_email').'>';
    $message = "Account Details on sample cloud \r\n User Name :- $user_login \r\n Password :- $email \r\n Log in url :- https://www.dmcloud.net/login";
    wp_mail( $email, $subject, $message, $headers );
  }

  function override_reg_complete_msg($errors, $redirect_to)
  {
    //print_r($errors);
    if (isset($errors->errors['registered'])) {
      $needle = __('Registration complete. Please check your e-mail.');
      foreach ($errors->errors['registered'] as $index => $msg) {
        if ($msg === $needle) {
          $errors->errors['registered'][$index] = 'Your '.get_bloginfo('name').' account and your Dynaamo SmartCloud account have been created';
        }
      }
    }
    return $errors;
  }

  function change_login_message($message)
	{
		// change messages that contain 'Register'
		if (strpos($message, 'Register') !== FALSE) {
			$newMessage = "You are creating an account on ".get_bloginfo('name')." and on Dynaamo powered by Sample. Your videos will be managed by Dynaamo.";
			return '<p class="message register">' . $newMessage . '</p>';
		}
		else {
			return $message;
		}
	}

  /**
   * Add options page
   */
  public function add_plugin_page()
  {
    // This page will be under "Settings"
    add_menu_page('Sample Admin Settings', 'Dynaamo', 'read', 'dm-admin-setting', array(
      $this,
      'create_admin_page'
    ));

    add_submenu_page('dm-admin-setting', 'Sample Admin Settings', 'Settings', 'read', 'dm-admin-setting', array(
      $this,
      'create_admin_page'
    ));
  }

  /**
   * Options page callback
   */
  public function create_admin_page()
  {
    global $user_meta;
    global $dm_option_auth;
    global $dm_session_store;
    global $pub_option_name;

    $sampleData = '';
    $options1        = ($user_meta) ? $user_meta : array();
    $options2        = ($pub_option_name) ? $pub_option_name : array();
    $options3        = ($dm_session_store) ? $dm_session_store : array();
    $options4        = ($dm_option_auth) ? $dm_option_auth : array();
    if (!empty($options4)) {
      $sampleData = $this->conectionSample();
    }
    $this->options = $options1 + $options2 + $options3;
    include_once("cloud_auth_form.php");
  }

  /**
   * Register and add settingshow to manage class exception in sample
   */
  public function page_init()
  {
    register_setting('dm_cloud_option_group', 'dm_cloud_option_name', array(
      $this,
      'sanitize'
    ));

    add_settings_section('setting_section_id', '', array(
      $this,
      'print_section_info'
    ), 'my-setting-admin');

    //<span class="qus_mark tooltip"><span><img class="callout" src="'.SAMPLE_URL . '/img/callout.gif" />The UserID is available on the profile page of your Sample Cloud account.</span></span>
    add_settings_field('cloud_user_id_number', 'UserID:', array(
      $this,
      'cloud_user_id_number_callback'
    ), 'my-setting-admin', 'setting_section_id');

    //<span class="qus_mark tooltip"><span><img class="callout" src="'.SAMPLE_URL . '/img/callout.gif" />The APIKey is available on the profile page of your Sample Cloud account.</span></span>
    add_settings_field('cloud_api_key', 'APIKey:', array(
      $this,
      'cloud_api_key_callback'
    ), 'my-setting-admin', 'setting_section_id');
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function sanitize($input)
  {
    $new_input = array();

    if (empty($input['cloud_user_id_number'])) {
      add_settings_error('cloud_user_id_number', 'cloud_user_id_number', "You enter your dmc user id", 'error');
    } else {
      $new_input['cloud_user_id_number'] = sanitize_text_field($input['cloud_user_id_number']);
    }

    if (empty($input['cloud_api_key'])) {
      add_settings_error('cloud_api_key', 'cloud_api_key', "You enter your api key", 'error');
    } else {
      $new_input['cloud_api_key'] = sanitize_text_field($input['cloud_api_key']);
    }

    if (!empty($input['cloud_user_id_number']) && !empty($input['cloud_api_key'])) {
      $userinfo = new SampleCloudOwnMethod($input['cloud_user_id_number'], $input['cloud_api_key']);
      try {
        $USerid = $userinfo->getSampleCloudUserInfo();
      }
      catch (Exception $e) {
        $msg = $e->getMessage();
        add_settings_error('cloud_api_key', 'cloud_api_key', "$msg", 'error');
      }
    }
    return $new_input;
  }

  /**
   * Print the Section text
   */
  public function print_section_info()
  {
    print '';
  }
  /**
   * Get the settings option array and print one of its values
   */
  public function cloud_user_id_number_callback()
  {
    printf('<input type="text" id="cloud_user_id_number" size="40" name="dm_cloud_option_name[cloud_user_id_number]" value="%s" />', isset($this->options['cloud_user_id_number']) ? esc_attr($this->options['cloud_user_id_number']) : '');
  }

  /**
   * Get the settings option array and print one of its values
   */
  public function cloud_api_key_callback()
  {
    printf('<input type="text" id="cloud_api_key" size="40" name="dm_cloud_option_name[cloud_api_key]" value="%s" />', isset($this->options['cloud_api_key']) ? esc_attr($this->options['cloud_api_key']) : '');
  }

  /**
   * Register and add settings
   */
  public function publish_id_setting_init()
  {
    register_setting('publish_id_option_group', 'publish_id_option_name', array(
      $this,
      'publish_id_sanitize'
    ));

    add_settings_section('publish_id_section_id', '', array(
      $this,
      'publish_id_info'
    ), 'publish-id-setting-admin');

    add_settings_field('dm_channel', '<span class="required">*</span>Default channel :', array(
      $this,
      'dm_channel_callback'
    ), 'publish-id-setting-admin', 'publish_id_section_id');

    add_settings_field('publisher_id', 'Publisher ID :', array(
      $this,
      'publisher_id_callback'
    ), 'publish-id-setting-admin', 'publish_id_section_id');

  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function publish_id_sanitize($input)
  {

    $new_input = array();

    if (empty($input['publisher_id'])) {
      add_settings_error('publisher_id', 'publisher_id', "You enter your publisher id", 'error');
    } else {
      $new_input['publisher_id'] = sanitize_text_field($input['publisher_id']);
    }

    if (empty($input['dm_channel'])) {
      add_settings_error('dm_channel', 'dm_channel', "Please enter Channel name.", 'error');
    } else {
      $new_input['dm_channel'] = sanitize_text_field($input['dm_channel']);
    }

    return $new_input;
  }


  /**
   * Print the Section text
   */
  public function publish_id_info()
  {
    print '';
  }


  /**
   * Get the settings option array and print one of its values
   */
  public function publisher_id_callback()
  {
    printf('<input type="text" id="publisher_id" size="40" name="publish_id_option_name[publisher_id]" value="%s" /><span class="qus_mark tooltip"><span><img class="callout" src="' . SAMPLE_URL . '/img/callout.gif" />
           Sample Publisher allows you to earn advertising revenue when sharing Sample videos on your site.</span></span>', !empty($this->options[0]['publisher_id']) ? esc_attr($this->options[0]['publisher_id']) : '');
  }

  /**
   * Get the settings option array and print one of its values
   */
  public function dm_channel_callback()
  {
    $chennels     = new SampleOwnMethod();
    $chennelslist = $chennels->getSampleChannelList();
    $output       = '<select type="text" id="dm_channel" name="publish_id_option_name[dm_channel]">';
    $output .= '<option value="">Please select</option>';

    if (isset($chennelslist) && !empty($chennelslist)) {
      foreach ($chennelslist as $ck => $cv) {
        (!empty($this->options[0]['dm_channel']) && ($this->options[0]['dm_channel'] == $ck)) ? $selected = 'selected="selected"' : $selected = '';
        $output .= '<option value="' . $ck . '" ' . $selected . '>' . $cv . '</option>';
      }
    }
    $output .= '</select><span class="qus_mark tooltip"><span><img class="callout" src="' . SAMPLE_URL . '/img/callout.gif" />Sample Publisher allows you to earn advertising revenue when sharing Sample videos on your site.</span></span>';
    print $output;
  }




  /**
   * Register and add settings
   */
  public function sample_setting_init()
  {
    register_setting('sample_option_group', 'sample_option_auth', array(
      $this,
      'sample_auth_sanitize'
    ));

    add_settings_section('sample_outh_section', '', array(
      $this,
      'sample_title_info'
    ), 'sample-outh-setting');

    add_settings_field('sample_apikey', 'API Key:', array(
      $this,
      'sample_apikey_callback'
    ), 'sample-outh-setting', 'sample_outh_section');

    add_settings_field('sample_secretkey', 'API Secret:', array(
      $this,
      'sample_secretkey_callback'
    ), 'sample-outh-setting', 'sample_outh_section');

  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function sample_auth_sanitize($input)
  {

    $new_input = array();

    if (empty($input['sample_apikey'])) {
      add_settings_error('sample_apikey', 'sample_apikey', "You enter your publisher id", 'error');
    } else {
      $new_input['sample_apikey'] = sanitize_text_field($input['sample_apikey']);
    }

    if (empty($input['sample_secretkey'])) {
      add_settings_error('sample_secretkey', 'sample_secretkey', "Please enter Channel name.", 'error');
    } else {
      $new_input['sample_secretkey'] = sanitize_text_field($input['sample_secretkey']);
    }

    return $new_input;
  }


  /**
   * Print the Section text
   */
  public function sample_title_info()
  {
    print '';
  }


  /**
   * Get the settings option array and print one of its values
   */
  public function sample_apikey_callback()
  {
    printf('<input type="text" id="sample_apikey" size="40" name="sample_option_auth[sample_apikey]" value="%s" />', isset($this->options['sample_apikey']) ? esc_attr($this->options['sample_apikey']) : '');
  }

  /**
   * Get the settings option array and print one of its values
   */
  public function sample_secretkey_callback()
  {
    printf('<input type="text" id="sample_secretkey" size="40" name="sample_option_auth[sample_secretkey]" value="%s" />', isset($this->options['sample_secretkey']) ? esc_attr($this->options['sample_secretkey']) : '');
  }


  public function sample_load_js_and_css()
  {
    global $hook_suffix;
    global $user_meta;
    global $dm_session_store;
    global $pub_option_name;

    $auth = '';
    $dm   = $dm_session_store;
    $dmc  = $user_meta;
    if (!empty($dm) && !empty($dmc)) {
      $auth = 'BOTH_CONNECTED';
    } else if (empty($dm) && !empty($dmc)) {
      $auth = 'ONLY_DMC_CONNECTED';
    } else if (!empty($dm) && empty($dmc)) {
      $auth = 'ONLY_DM_CONNECTED';
    } else if (empty($dm) && empty($dmc)) {
      $auth = 'BOTH_DISCONNECTED';
    }

    $publisherSettings = @$pub_option_name;
    $publisherId       = (!empty($publisherSettings[0]['publisher_id'])) ? $publisherSettings[0]['publisher_id'] : '';
    $parameter         = (isset($publisherId) && !empty($publisherId)) ? "?syndication=$publisherId" : '';
    $pluginurl         = SAMPLE_URL;
    if (in_array($hook_suffix, array(
      'toplevel_page_dm-admin-setting',
      'dynaamo_page_dm-video-gallery',
      'dynaamo_page_video-gallery-page',
      'admin_page_dm-video-gallery'
    )) ) {
      wp_register_style('sample.css', $pluginurl . '/css/sample.css', array(), '2.5.9');
      wp_enqueue_style('sample.css');

      wp_register_style('jquery.fancybox-1.3.4.css', $pluginurl . '/css/jquery.fancybox-1.3.4.css', array(), '2.5.9');
      wp_enqueue_style('jquery.fancybox-1.3.4.css');

      wp_register_style('jquery.tagsinput.css', $pluginurl . '/css/jquery.tagsinput.css', array(), '2.5.9');
      wp_enqueue_style('jquery.tagsinput.css');

      wp_register_style('jquery-ui.css', $pluginurl . '/css/jquery-ui.css', array(), '2.5.9');
      wp_enqueue_style('jquery-ui.css');

      wp_register_style('pagination.css', $pluginurl . '/css/pagination.css', array(), '2.5.9');
      wp_enqueue_style('pagination.css');

      wp_register_script('jquery-1.7.2.min.js', $pluginurl . '/js/jquery-1.7.2.min.js', array(
        'jquery'
      ), '2.5.9');
      wp_enqueue_script('jquery-1.7.2.min.js');

      wp_register_script('jquery.tagsinput.js', $pluginurl . '/js/jquery.tagsinput.js', array(
        'jquery'
      ), '2.5.9');
      wp_enqueue_script('jquery.tagsinput.js');

      wp_register_script('jquery-ui.min.js', $pluginurl . '/js/jquery-ui.min.js', array(
        'jquery'
      ), '2.5.9');
      wp_enqueue_script('jquery-ui.min.js');

      wp_register_script('jquery.fancybox-1.3.4.js', $pluginurl . '/js/jquery.fancybox-1.3.4.js', array(
        'jquery'
      ), '2.5.9');
      wp_enqueue_script('jquery.fancybox-1.3.4.js');

      wp_register_script('sample.js', $pluginurl . '/js/sample.js', array(
        'jquery'
      ), '2.5.9');
      wp_enqueue_script('sample.js');

      wp_localize_script('sample.js', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'connect_url' => admin_url('admin.php') . '?page=dm-admin-setting',
        'upload_url' => admin_url('admin.php') . '?page=video-upload',
        'parameter' => $parameter
      ));

      wp_enqueue_script('ajax-upload-pattern', $pluginurl . '/js/ajax-upload_pattern.js', array(
        'plupload-all',
        'jquery'
      ), 1.0);
      wp_localize_script('ajax-upload-pattern', 'ajax_object_another', array(
        'ajaxurl' => admin_url('admin-ajax.php')
      ));

      wp_register_script('all.js', 'http://api.dmcdn.net/all.js', array(
        'jquery'
      ), '2.5.9');
      wp_enqueue_script('all.js');


      wp_register_script('analytic', $pluginurl . '/js/analytic.js', array(
        'jquery'
      ), '2.5.9', true);
      wp_enqueue_script('analytic');

    } elseif (in_array($hook_suffix, array('post-new.php', 'post.php')) || (is_page() || is_single()) ) {

      wp_register_script('jquery-1.7.2.min.js', $pluginurl . '/js/jquery-1.7.2.min.js', array(
        'jquery'
      ), '2.5.9');
      wp_enqueue_script('jquery-1.7.2.min.js');

      if ((is_page() || is_single())) {
        wp_enqueue_script( 'media-upload' );
      }

      wp_register_script('jquery.clipboard.js', $pluginurl . '/js/clipboard/jquery.clipboard.js', array(
        'jquery'
      ), '1.0.4');
      wp_enqueue_script('jquery.clipboard.js');

      wp_register_script('jquery.hoverIntent.js', $pluginurl . '/js/jquery.hoverIntent.js', array(
        'jquery'
      ), '2.5.9');
      wp_enqueue_script('jquery.hoverIntent.js');

      wp_register_script('metabox_scripts.js', $pluginurl . '/js/metabox_scripts.js', array(
        'jquery'
      ), '2.5.9');
      wp_enqueue_script('metabox_scripts.js');

      wp_localize_script('metabox_scripts.js', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'sample_url' => SAMPLE_URL,
        'auth_status' => $auth,
        'connect_url' => admin_url('admin.php') . '?page=dm-admin-setting',
        'upload_url' => admin_url('admin.php') . '?page=video-upload',
        'parameter' => $parameter,
        'check_on_theme' => ((is_page() || is_single())) ? true : false,
        'front_page_url' => get_permalink(get_option('dmcauthenticate_page_id')),
      ));

      wp_register_style('metabox.css', $pluginurl . '/css/metabox.css', array(), '2.5.9');
      wp_enqueue_style('metabox.css');

      wp_register_style('jquery.fancybox-1.3.4.css', $pluginurl . '/css/jquery.fancybox-1.3.4.css', array(), '2.5.9');
      wp_enqueue_style('jquery.fancybox-1.3.4.css');

      wp_register_script('jquery.fancybox-1.3.4.js', $pluginurl . '/js/jquery.fancybox-1.3.4.js', array(
        'jquery'
      ), '2.5.9');
      wp_enqueue_script('jquery.fancybox-1.3.4.js');

      wp_register_style('sample.css', $pluginurl . '/css/sample.css', array(), '2.5.9');
      wp_enqueue_style('sample.css');

      wp_register_script('sample.js', $pluginurl . '/js/sample.js', array(), '2.5.9');
      wp_enqueue_script('sample.js');

      wp_register_script('all.js', 'http://api.dmcdn.net/all.js', array(), '2.5.9');
      wp_enqueue_script('all.js');

      wp_register_script('analytic', $pluginurl . '/js/analytic.js', array(), '2.5.9', true);
      wp_enqueue_script('analytic');
    } else {
      wp_register_style('menu.css', $pluginurl . '/css/menu.css', array(), '2.5.9');
      wp_enqueue_style('menu.css');
    }
  }

  /**
   * Save cloud setting in option table
   */
  public function update()
  {
    $message = new stdClass();
    global $current_user;
    global $dm_option_auth;
    global $pub_option_name;

    switch ($_POST['option_page']) {
      case 'dm_cloud_option_group':
        $nonce = $_REQUEST['_wpnonce'];
        $check_front = !empty($_REQUEST['_front_flag']) ? $_REQUEST['_front_flag'] : '';

        if ( !empty($check_front) && !wp_verify_nonce( $nonce, 'dmc-front-auth' ) ) {
            wp_die( __( 'Cheating?' ) );
        }

        if (!empty($_POST['dm_cloud_option_name']['cloud_user_id_number']) && !empty($_POST['dm_cloud_option_name']['cloud_api_key']) && !empty($current_user->ID)) {
          $userinfo = new SampleCloudOwnMethod($_POST['dm_cloud_option_name']['cloud_user_id_number'], $_POST['dm_cloud_option_name']['cloud_api_key']);
          try {
            $USerid = $userinfo->getSampleCloudUserInfo();
            $user_meta = get_user_meta($current_user->ID, 'dmcloud_api_secret', false);
            if (empty($user_meta)) {
              add_user_meta($current_user->ID, 'dmcloud_api_secret', $_POST['dm_cloud_option_name']);
            } else {
              update_user_meta($current_user->ID, 'dmcloud_api_secret', $_POST['dm_cloud_option_name']);
            }
            $message->success = 'Settings saved successfully.';
            print json_encode($message);
            exit();
          }
          catch (Exception $e) {
            $msg            = $e->getMessage();
            $message->error = $msg;
            print json_encode($message);
            exit();
          }
        } else {
          $message->error = 'Alert: Incorrect UserId or API Key. Please try again.';
          print json_encode($message);
          exit();
        }
        break;

      case 'publish_id_option_group':
        if (!empty($_POST['publish_id_option_name']['dm_channel'])) {
          if (!empty($pub_option_name)) {
            update_user_meta($current_user->ID, 'publish_id_option_name', $_POST['publish_id_option_name']);
          } else {
            add_user_meta($current_user->ID, 'publish_id_option_name', $_POST['publish_id_option_name']);
          }
          $message->success = 'Settings saved successfully.';
          print json_encode($message);
          exit();
        } else if (empty($_POST['publish_id_option_name']['dm_channel'])) {
          $message->error = 'Please select a channel.';
          print json_encode($message);
          exit();
        }
        break;

      case 'sample_option_group':
        $option_name = 'sample_option_auth';
        if (!empty($_POST['sample_option_auth']['sample_apikey']) && !empty($_POST['sample_option_auth']['sample_secretkey'])) {
          if (!empty($dm_option_auth)) {
            update_user_meta($current_user->ID, 'sample_option_auth', $_POST['sample_option_auth']);
          } else {
            add_user_meta($current_user->ID, 'sample_option_auth', $_POST['sample_option_auth']);
          }
          $SampleOwnMethod = new SampleOwnMethod();
          try {
            $conecctionresult = $SampleOwnMethod->getSampleConnectedInformation();
          }
          catch (SampleAuthRequiredException $e) {
            $message->success = $SampleOwnMethod->getDMAuthorizationUrl('popup');
            print json_encode($message);
            exit();
          }
          catch (SampleAuthRefusedException $e) {
            $message->error = ($e->getMessage());
            print json_encode($message);
            exit();
          }
          catch (InvalidArgumentException $e) {
            $message->error = ($e->getMessage());
            print json_encode($message);
            exit();
          }
        } else if (empty($_POST['sample_option_auth']['sample_apikey'])) {
          $message->error = 'Please enter your api key.';
          print json_encode($message);
          exit();
        } else if (empty($_POST['sample_option_auth']['sample_secretkey'])) {
          $message->error = 'Please enter your secret key.';
          print json_encode($message);
          exit();
        }
        break;

      default:
        break;
    }
  }

  /**
   * Discconect both account
   */
  public function discconetAccount()
  {
    $message = new stdClass();
    global $current_user;
    switch ($_POST['account_name']) {
      case 'cloud':

        delete_user_meta($current_user->ID, 'dmcloud_api_secret' );
        $message->success = 'Account disconnected successfully.';
        print json_encode($message);
        exit();
        break;

      case 'sample':
        delete_user_meta($current_user->ID, 'sample_session_store' );
        delete_user_meta($current_user->ID, 'publish_id_option_name' );
        $message->success = 'Account disconnected successfully.';
        print json_encode($message);
        exit();
        break;

      default:
        break;
    }
  }
  
  public function createCloudAccount() {
   // echo "wefwefwef";die;
    $current_user = wp_get_current_user();
    $username =  $current_user->user_login;
    $useremail = $current_user->user_email;
    $user_id = '53a17d14947399435432a24f';
    $api_key = '853c339e3f50a3e3ad9443db2bd12315cfde26ad';
    
    $DmcOwnMethod = new SampleCloudOwnMethod($user_id, $api_key);
    $returndata   = (array) $DmcOwnMethod->createNewUserOnOrganigation($user_id, $username, $useremail);
    if (isset($returndata['id']) && !empty($returndata['id'])) {
      $subject = 'Sample cloud Account Detail';
      $headers[] = 'From: '.get_option('blogname').' <'.get_option('admin_email').'>';
      $message = "Account Details on sample cloud \r\n User Name :- $username \r\n Password :- $useremail \r\n Log in url :- https://www.dmcloud.net/login";
      wp_mail( $useremail, $subject, $message, $headers );
      echo 'SUCCESS'; die;
    } else {
      echo 'FAILURE';die;
    }
  }

  /**
   * function to show data after account connected
   */
  public function showCloudaccountData()
  {
    global $user_meta;
    if (isset($user_meta[0]['cloud_user_id_number']) && isset($user_meta[0]['cloud_api_key'])) {
      $userinfo = new SampleCloudOwnMethod($user_meta[0]['cloud_user_id_number'], $user_meta[0]['cloud_api_key']);
      $data     = $userinfo->getConnectedInformation();
      return $data;
    }
  }

  /**
   *
   */
  public function conectionSample()
  {
    $SampleOwnMethod = new SampleOwnMethod();

    try {
      $conecctionresult = $SampleOwnMethod->getSampleConnectedInformation();
      return $conecctionresult;
    }
    catch (SampleAuthRequiredException $e) {
      return $SampleOwnMethod->getDMAuthorizationUrl('popup');
    }
    catch (SampleAuthRefusedException $e) {
      print($e->getMessage());
      die;
    }
    catch (SampleApiException $e) {
      print($e->getMessage());
      die;
    }
    catch (SampleTransportException $e) {
      print($e->getMessage());
      die;
    }
  }

}
