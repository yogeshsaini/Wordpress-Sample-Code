<?php
class VideoUpload
{
    protected $usname;
    protected $appsecret;
    public $chennelname;
    public function __construct($dmcusname = null, $dmcapikey = null)
    {
        $this->usname = $dmcusname;
        $this->appsecret = $dmcapikey;
        $defaultChannel = get_option('publish_id_option_name');
        
        if ( !empty($defaultChannel['dm_channel']) ) {
             $this->chennelname = $defaultChannel['dm_channel'];
        }
        add_action('admin_menu', array(
            $this,
            'addVideoUploadPage'
        ));
        add_action('admin_enqueue_scripts', array(
            $this,
            'pluAdminEnqueue'
        ));
        add_action("admin_head", array(
            $this,
            "pluploadAdminHead"
        ));
        add_filter('upload_mimes', array(
            &$this,
            'allowedMimeTypes'
        ), 1, 1);
        add_action('wp_ajax_plupload_action', array(
            $this,
            "cloudAndDmUploadAction"
        ));
    }
    /**
     * Add options page
     */
    public function addVideoUploadPage()
    {
        // This page will be under "Settings"
        add_submenu_page('dm-admin-setting', 'Upload', 'Upload', 'administrator', 'video-upload', array(
            $this,
            'createVideoUploadAdminPage'
        ));
    }
    /**
     * Options page callback
     */
    public function createVideoUploadAdminPage()
    {
        include_once("video_upload_html.php");
    }
    /**
     * Options page callback
     */
    public function pluAdminEnqueue()
    {
        global $hook_suffix;
        $pluginurl = SAMPLE_URL;
        if (in_array($hook_suffix, array('sample_page_video-upload'))) {
            
            wp_enqueue_script('plupload-all');
            wp_register_script('video_plupload', $pluginurl . '/video_upload/js/video_plupload.js', array('jquery'));
            wp_enqueue_script('video_plupload');
            wp_localize_script('video_plupload', 'video_plupload_object', array(
                'plugin_url' => SAMPLE_URL
            ));
            wp_register_style('video_plupload', $pluginurl . '/video_upload/css/video_plupload.css');
            wp_enqueue_style('video_plupload');
            
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
            
            wp_register_script('jquery.tagsinput.js', $pluginurl . '/js/jquery.tagsinput.js', array('jquery'), '2.5.9');
            wp_enqueue_script('jquery.tagsinput.js');
            
            wp_register_script('jquery-ui.min.js', $pluginurl . '/js/jquery-ui.min.js', array('jquery'), '2.5.9');
            wp_enqueue_script('jquery-ui.min.js');
            
            wp_register_script('jquery.fancybox-1.3.4.js', $pluginurl . '/js/jquery.fancybox-1.3.4.js', array('jquery'), '2.5.9');
            wp_enqueue_script('jquery.fancybox-1.3.4.js');
            
            wp_register_script('sample.js', $pluginurl . '/js/sample.js', array('jquery'), '2.5.9');
            wp_enqueue_script('sample.js');
            
            wp_localize_script('sample.js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php') ));
            
            wp_enqueue_script('ajax-upload-pattern', $pluginurl . '/js/ajax-upload_pattern.js', array('jquery'), 1.0);
            wp_localize_script('ajax-upload-pattern', 'ajax_object_another', array(
                'ajaxurl' => admin_url('admin-ajax.php')
            ));
            
            wp_register_script('analytic', $pluginurl . '/js/analytic.js', array('jquery'), '2.5.9', true);
            wp_enqueue_script('analytic');
        }
    }
    /**
     * Options page callback
     */
    public function pluploadAdminHead()
    {
        // place js config array for plupload
        $plupload_init = array(
            'runtimes' => 'html5,silverlight,flash,html4',
            'browse_button' => 'plupload-browse-button', // will be adjusted per uploader
            'container' => 'plupload-upload-ui', // will be adjusted per uploader
            'drop_element' => 'plupload-upload-ui', // will be adjusted per uploader
            'file_data_name' => 'async-upload', // will be adjusted per uploader
            'multiple_queues' => true,
            'dragdrop' => true,
            'max_file_size' => wp_max_upload_size() . 'b',
            'url' => admin_url('admin-ajax.php'),
            'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
            'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
            'filters' => array(
                array(
                    'title' => __('Allowed Files'),
                    'extensions' => 'mp4,avi,3gp,flv'
                )
            ),
            'multipart' => true,
            'urlstream_upload' => true,
            'multi_selection' => false, // will be added per uploader
            // additional post data to send to our ajax hook
            'multipart_params' => array(
                '_ajax_nonce' => "", // will be added per uploader
                'action' => 'plupload_action', // the ajax action name
                'video_id' => 0 // will be added per uploader
            )
        );
    ?>
        <script type="text/javascript">
        var base_plupload_config=<?php echo json_encode($plupload_init); ?>;
        </script>
    <?php
    }
    /**
     * Options page callback
     */
    public function cloudAndDmUploadAction()
    {
        $video_id = $_POST["video_id"];
        check_ajax_referer($video_id . 'pluploadan');
        switch ($_POST['account_name']) {
            case 'cloud':
                $status = wp_handle_upload($_FILES[$video_id . 'async-upload'], array(
                    'test_form' => false,
                    'action' => 'plupload_action'
                ));
                if (!isset($status['error']) && isset($status['file'])) {
                    $info     = pathinfo($status['file']);
                    $filesize = filesize($status['file']);
                    try {
                        
                        $obj_samplecloud = new SampleCloudOwnMethod($this->usname, $this->appsecret);
                        $media_id             = $obj_samplecloud->uploadSampleCloudVideo($status['file'], $info['filename']);
                        unlink($status['file']);
                        print str_replace(",", "_", $info['filename']) . '|+|' . $media_id->id . '|+|' . $status['url'] . '|+|' . $this->fileSizeConvert($filesize) . '|+|' . $_POST['account_name'];
                        exit;
                    }
                    catch (Exception $e) {
                        $filename = $info['filename'];
                        unlink($status['file']);
                        error_log($e->getMessage(), 0);
                        error_log($e->getMessage(), 1, "anurag.bhargava@daffodilsw.com");
                        die("error|+|$filename|+|Failed to transfer on sample cloud");
                    }
                } else {
                    $filename = $info['filename'];
                    die("error|+|$filename|+|Failed to open input stream");
                    exit;
                }
                break;
            case 'sample':
                $status = wp_handle_upload($_FILES[$video_id . 'async-upload'], array(
                    'test_form' => false,
                    'action' => 'plupload_action'
                ));
                if (!isset($status['error']) && isset($status['file'])) {
                    $info     = pathinfo($status['file']);
                    $filesize = filesize($status['file']);
                    try {
                        $obj_sample = new SampleOwnMethod();
                        $media_id        = (object) $obj_sample->uploadVideoOnSample($status['file'], $info['filename'], $this->chennelname);
                        unlink($status['file']);
                        print str_replace(",", "_", $info['filename']) . '|+|' . $media_id->id . '|+|' . $status['url'] . '|+|' . $this->fileSizeConvert($filesize) . '|+|' . $_POST['account_name'];
                        exit;
                    }
                    catch (Exception $e) {
                        $filename = $info['filename'];
                        unlink($status['file']);
                        error_log($e->getMessage(), 0);
                        error_log($e->getMessage(), 1, "anurag.bhargava@daffodilsw.com");
                        die("error|+|$filename|+|Failed to transfer on sample");
                    }
                } else {
                    $filename = $info['filename'];
                    die("error|+|$filename|+|Failed to open input stream");
                    exit;
                }
                break;
            default:
                break;
        }
    }
    /**
     * Options page callback
     */
    public function allowedMimeTypes($mime_types)
    {
        //print_r($mime_types); die;
        $mime_types['3gp'] = 'video/3gpp'; //Adding avi extension
        //$mime_types['avi'] = 'video/3gpp'; //Adding avi extension
        //unset($mime_types['pdf']); //Removing the pdf extension
        return $mime_types;
    }
    
    /**
     * Get file size
     */
    public function fileSizeConvert($bytes)
    {
        $bytes   = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            )
        );
        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                $result = strval(round($result, 2)) . ' ' . $arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

}
//new VideoUpload();