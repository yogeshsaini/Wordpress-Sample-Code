<div id="header-container" class="wrap">
   <h2><?php _e('Settings', 'sample'); ?></h2>
   <?php if (   !empty($options3) || !empty($options1) ) : ?>
   <p><?php  _e(  'Congrats, you\'re now connected!', 'sample' ); ?><br/>
      <?php  _e(  'You can start managing your videos using the Sample menus to the left.', 'sample'  ); ?>
   </p>
   <?php else : ?>
   <p><?php  _e(  'Insert your video into your post while you writing.', 'sample'   ); ?><br/>
      <?php  _e(  'Connect Your Sample account(s) below.', 'sample'  ); ?>
   </p>
   <?php endif; ?>
   <div class="box_wrapper">
      <?php if (  isset(   $sample_data ) && !empty(   $sample_data ) && is_array( $sample_data )  ) : ?>
      <div id="sample_box">
         <div class="align_center">
            <div class="header_logo first"><img src="<?php print SAMPLE_URL; ?>/assets/img/dm_head.jpg" alt="" /></div>
            <div id="sample_box_conected">
               <div class="right_arraow"><img src="<?php print SAMPLE_URL; ?>/assets/img/right_sign.jpg" alt="" /></div>
               <div class="connected"><?php _e('Your Sample account is connected', 'sample'); ?></div>
            </div>
            <div id="sample_box_conected" class="conected_second">
               <div class="account_picture"><img src="<?php print $sample_data['user_photo']; ?>" alt="" /></div>
               <div class="account_name"><?php print $sample_data['screenname']; ?></div>
               <div class="disconnect_wrapper">
                  <a class="disconnect_account" rel="sample" href="#"><?php _e('Disconnect', 'sample'); ?></a>
                  <img id="sample_throbber" src="<?php print SAMPLE_URL; ?>/assets/img/throbber.gif" alt="" class="displaynone" />
               </div>
               <div class="total_video">
                  <div><?php _e('Total videos'); ?></div>
                  <div class="span_count"><?php  print $sample_data['total_record']; ?></div>
               </div>
               <div class="total_video">
                  <div><?php _e('Last uploaded'); ?></div>
                  <div class="span_count"><?php  print $sample_data['last_uploaded']; ?></div>
               </div>
            </div>
         </div>
         <div id="header-container" class="wrap user_publish_setting">
            <h2 class="blankh2">&nbsp;</h2>
            <div class="wrap">
               <h2><?php _e('Publication Settings', 'sample'); ?></h2>
               <?php settings_errors(); ?>
               <form id="publication_settings_form" method="post" action="options.php" onsubmit="return publication_settings_form_submit(this);">
                  <?php settings_fields('publish_id_option_group'); do_settings_sections('publish-id-setting-admin'); ?>
                  <div class="wht_pub">
                     <div class="left_pub"></div>
                     <div class="right_pub">
                        <h3><?php _e('What is Publisher?', 'sample'); ?></h3>
                        <p>Sample Publisher allows you to earn advertising revenue when sharing Sample videos on your site.</p>
                        <p><a target="_balnk" href="http://publisher.sample.com/">Join Publisher now</a> - it's free!</p>
                     </div>
                  </div>
                  <div id="publicationmessage"></div>
                  <?php submit_button('Save Publication Settings'); ?>
               </form>
            </div>
         </div>
      </div>
      <?php else: ?>
      <div id="sample_box">
         <div class="align_center">
            <div class="header_logo first"><img src="<?php print SAMPLE_URL; ?>/assets/img/dm_head.jpg" alt="" /></div>
            <?php if(empty($options4)) : ?>
            <a class="dm_pop_btn" id="dm_auth_popup" href="#sample_form_popup"><?php _e('Connect to Sample', 'sample'); ?></a>
            <?php else : ?>
            <a class="dm_pop_btn" href="<?php print $sample_data; ?>"><?php _e('Connect to Sample', 'sample'); ?></a>
            <?php endif; ?>
            <a class="sub_link" id="sub_link" target="_balnk" href="#sample_form_popup"><?php _e('or create a sample account', 'sample'); ?></a>
         </div>
      </div>
      <div>
         <div id="sample_form_popup">
            <div class="connect_heading">Connect to Sample.com</div>
            <div class="wrap11">
               <form id="sample_outh_form" method="post" action="options.php" onsubmit="return sample_settings_form_submit(this);">
                  <?php settings_fields('sample_option_group'); do_settings_sections('sample-outh-setting'); ?>
                  <div id="sample_message"></div>
                  <?php submit_button('Save'); ?>
                  <p class="submit">Don't have an API Key yet? <a href="javascript:void(0);" class="show_account_desc">Create one</a></p>
               </form>
               <div class="create_account_desc">
                  <h3>How to create an API Key</h3>
                  <ol>
                     <li>Connect with your Sample account on <br> <a target="_balnk" href="http://www.sample.com/profile/developer">http://www.sample.com/profile/developer</a></li>
                     <li>If necessary, click on the button "Create a new API Key" to create a new form</li>
                     <li>
                        Enter the following info in the form: <br>
                        <ul>
                           <li>Name of you app: "My wordpress/Drupal app"</li>
                           <li>Application website url: <a href="javascript:void(0);"><?php bloginfo('url'); ?></a></li>
                           <li>Callback url: <a href="javascript:void(0);"><?php print admin_url('admin.php').'?page=dm-admin-setting'; ?></a></li>
                        </ul>
                     </li>
                     <li>Save the form</li>
                     <li>Copy your API key and secret in the <a href="javascript:void(0);" class="hide_account_desc">Form here</a></li>
                  </ol>
               </div>
            </div>
         </div>
      </div>
      <?php endif; ?>
      <?php if (   isset($options1) && !empty($options1)  ) : ?>
      <div id="sample_box" class="dmc-box">
         <div class="align_center">
            <div class="header_logo"><img src="<?php print SAMPLE_URL; ?>/assets/img/dm_cloud.jpg" alt="" /></div>
            <div id="sample_box_conected">
               <div class="right_arraow"><img src="<?php print SAMPLE_URL; ?>/assets/img/right_sign.jpg" alt="" /></div>
               <div class="connected"><?php _e('Your Sample Cloud account is connected', 'sample'); ?></div>
            </div>
            <div id="sample_box_conected" class="conected_second">
               <?php $data = $this->sp_show_cloud_account_data(); ?>
               <div class="account_name"><?php print $data['udata']; ?></div>
               <div class="disconnect_wrapper">
                  <a class="disconnect_account" rel="cloud" href="#"><?php _e('Disconnect', 'sample'); ?></a>
                  <img id="cloud_throbber" src="<?php print SAMPLE_URL; ?>/assets/img/throbber.gif" alt="" class="displaynone" />
               </div>
               <div class="total_video">
                  <div><?php _e('Total videos'); ?></div>
                  <div class="span_count"><?php  print $data['mdata']; ?></div>
               </div>
               <div class="total_video">
                  <div><?php _e('Last uploaded'); ?></div>
                  <div class="span_count"><?php  print $data['last_uploaded']; ?></div>
               </div>
            </div>
         </div>
      </div>
      <?php else: ?>
      <div id="sample_box" class="cloudbox">
         <div class="align_center">
            <div class="header_logo"><img src="<?php print SAMPLE_URL; ?>/assets/img/dynaamo-logo.png" alt="" /></div>
            <a id="cloud_form_link" class="cloud_pop_btn" href="#cloud_form_popup"><?php _e('Connect to your Dynaamo SmartCloud Account', 'sample'); ?></a>
            <a class="sub_link" id="cloud_register_trigger" href="javascript:void(0);"><?php _e('<strong>Your videos are hosted for Free but are public.</strong>', 'sample'); ?></a>
         </div>
      </div>
      <?php endif; ?>
   </div>
   <?php if (   empty(  $options1   )  ) : ?>
   <?php //settings_errors(); ?>
   <div>
      <div id="cloud_form_popup">
         <div class="connect_heading"><?php _e('Connect to Sample Cloud', 'sample'); ?></div>
         <div class="wrap11">
            <form id="cloud_settings_form" method="post" action="options.php" onsubmit="return cloud_settings_form_submit(this);">
               <?php settings_fields('dm_cloud_option_group'); do_settings_sections('my-setting-admin'); ?>
               <div id="message"></div>
               <?php submit_button('Connect my account'); ?>
            </form>
         </div>
      </div>
   </div>
   <?php endif; ?>
</div>