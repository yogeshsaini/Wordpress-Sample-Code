<?php
   global $user_meta;
   global $dm_session_store;
   $id = "video_id1";
   $svalue = "";
   $multiple = true;
   $cloudcredents = $user_meta;
   $samplecredents = $dm_session_store;
   ?>
<?php if (empty($cloudcredents) && empty($samplecredents)) : ?>
<div id="full_wrapper" class="wrap dm-common">
   <h2><?php esc_attr_e('Upload Videos'); ?></h2>
   <div class="main-gallery-container">
      <div class="infomation_form" id="sample_cloud_div">
         <div class="wrap">
            <div class="dmc-not-auth">
               <div class="icon"></div>
               <div class="msg">
                  <p><?php esc_attr_e('You are not connected to an account on Sample cloud.'); ?></p>
                  <p><?php esc_attr_e('In order to see your videos here, go to the plug-in setting to connect your Sample Cloud account.'); ?></p>
               </div>
               <div class="link"><a href="<?php echo SAMPLE_ADMIN_URL; ?>admin.php?page=dm-admin-setting"><?php esc_attr_e('Go to Settings'); ?></a></div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php elseif (!empty($cloudcredents) || !empty($samplecredents)) : ?>
<div id="full_wrapper" class="wrap">
   <h2><?php esc_attr_e('Upload Videos'); ?></h2>
   <div class="overlay"></div>
   <div class="upload_message"></div>
   <div class="filelist"></div>
   <div class="plupload-thumbs <?php if ($multiple): ?>plupload-thumbs-multiple<?php endif; ?>" id="<?php echo $id; ?>plupload-thumbs"></div>
   <div class="action_wrappers">
      <label>Upload videos in :</label>
      <select id="account_name" name="account_name">
         <?php if (!empty($cloudcredents)) : ?>
         <option value="cloud"><?php esc_attr_e('Sample Cloud'); ?></option>
         <?php endif; ?>
         <?php if (!empty($samplecredents)) : ?>
         <option value="sample"><?php esc_attr_e('Sample.com'); ?></option>
         <?php endif; ?>
      </select>
      <a href="#" id="submitPattern"><?php esc_attr_e('Upload Videos'); ?></a>
   </div>
   <div id="plupload-upload-ui">
      <input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $svalue; ?>" />
      <div class="drag-drop-area plupload-upload-uic hide-if-no-js <?php if ($multiple): ?>plupload-upload-uic-multiple<?php endif; ?>" id="<?php echo $id; ?>plupload-upload-ui">
         <div class="drop_wrapper">
            <p><?php esc_attr_e('Drag and drop your video here'); ?></p>
            <p><?php esc_attr_e('or'); ?></p>
            <input id="<?php echo $id; ?>plupload-browse-button" type="button" value="<?php esc_attr_e('Browse files to upload'); ?>" class="button" />
         </div>
         <span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($id . 'pluploadan'); ?>"></span>
      </div>
   </div>
   <div class="clear"></div>
   <ul class="upload_desc">
      <li>- <?php printf( __( 'Your server maximum upload file size is %s. If you want to increase this limit please change in your server php.ini file.' ), $this->file_size_convert(wp_max_upload_size()) ); ?></li>
      <li>- <?php esc_attr_e('Recommended formats: mp4 (H264), mov, wmv, avi'); ?></li>
      <li>- <?php esc_attr_e('Recommended resolution: 640x480, 1280x720 or 1920x1080'); ?></li>
      <li>- <?php esc_attr_e('Recommended frequency: 25 frames per second'); ?></li>
   </ul>
</div>
<a href="#dm-cloud-edit-form" id="editpopup"></a>
<div class="dmc-edit-form-main">
   <div id="dm-cloud-edit-form" class="dm-cloud-edit-form"></div>
</div>
<?php endif; ?>