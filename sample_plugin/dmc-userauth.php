<?php

/**
 * Add Post form class
 *
 * @author Tareq Hasan
 * @package WP User Frontend
 */
class Dmc_Front_Outh_Form {
                
                function __construct( ) {
                                add_shortcode( 'dmc_authform', array(
                                                 $this,
                                                'shortcode' 
                                ) );
                }
                
                /**
                 * Handles the add post shortcode
                 *
                 * @param $atts
                 */
                function sp_shortcode( $atts ) {
                                
                                extract( shortcode_atts( array(
                                                 'post_type' => 'post' 
                                ), $atts ) );
                                
                                ob_start();
                                
                                if ( is_user_logged_in() ) {
                                                $this->dmc_auth_form( $post_type );
                                } else {
                                                printf( __( "This page is restricted. Please %s to view this page.", 'wpuf' ), wp_loginout( get_permalink(), false ) );
                                }
                                
                                $content = ob_get_contents();
                                ob_end_clean();
                                
                                return $content;
                }
                
                /**
                 * Add posting main form
                 *
                 * @param $post_type
                 */
                function sp_dmc_auth_form( $post_type )  {
                                global $userdata;
                                global $user_meta;
                                global $dm_settings_page;
                                
                                
                                if ( isset( $_POST['wpuf_post_new_submit'] ) ) {
                                                $nonce = $_REQUEST['_wpnonce'];
                                                if ( !wp_verify_nonce( $nonce, 'dmc-auth-form' ) ) {
                                                                wp_die( __( 'Cheating?' ) );
                                                }
                                                
                                                $this->submit_post();
                                }
                                
                                if ( isset( $user_meta ) && !empty( $user_meta ) ):
?>
    <div class="box_wrapper">
      <div id="sample_box" class="dmc-box front">
         <div class="align_center">
            <div class="header_logo"><img src="<?php print SAMPLE_URL; ?>/assets/img/dm_cloud.jpg" alt="" /></div>
            <div id="sample_box_conected">
               <div class="right_arraow"><img src="<?php print SAMPLE_URL; ?>/assets/img/right_sign.jpg" alt="" /></div>
               <div class="connected"><?php _e( 'Your Sample Cloud account is connected', 'sample' ); ?></div>
            </div>
            <div id="sample_box_conected" class="conected_second">
               <?php
                                                $data = $dm_settings_page->sp_show_cloud_account_data();
?>
              <div class="account_name"><?php
                                                print $data['udata'];
?></div>
               <div class="disconnect_wrapper">
                  <a class="disconnect_account" rel="cloud" href="#"><?php  _e( 'Disconnect', 'sample' ); ?></a>
                  <img id="cloud_throbber" src="<?php print SAMPLE_URL; ?>/assets/img/throbber.gif" alt="" class="displaynone" />
               </div>
               <div class="total_video">
                  <div><?php  _e( 'Total videos' ); ?></div>
                  <div class="span_count"><?php print $data['mdata']; ?></div>
               </div>
               <div class="total_video">
                  <div><?php _e( 'Last uploaded' ); ?></div>
                  <div class="span_count"><?php print $data['last_uploaded']; ?></div>
               </div>
            </div>
         </div>
      </div>
      </div>
      <?php
                                else:
?>
   <div class="box_wrapper">
      <div id="sample_box" class="cloudbox front">
         <div class="align_center">
            <div class="header_logo"><img src="<?php  print SAMPLE_URL; ?>/assets/img/dm_cloud.jpg" alt="" /></div>
            <a id="cloud_form_link" class="cloud_pop_btn" href="#cloud_form_popup"><?php  _e( 'Connect your Sample Cloud account', 'sample' ); ?></a>
            <a class="sub_link" target="_balnk" href="https://www.dmcloud.net/"><?php  _e( 'or start a free trail with Sample Cloud', 'sample' ); ?></a>
         </div>
      </div>
      </div>
      <div style="display: none">
        <div id="cloud_form_popup" style="float:left;">
           <div class="connect_heading"><?php _e( 'Connect to Sample Cloud', 'sample' ); ?></div>
           <div class="wrap11">
              <form onsubmit="return cloud_settings_form_submit(this);" action="options.php" method="post" id="cloud_settings_form">
                 <?php wp_nonce_field( 'dmc-front-auth' ); ?>
                <input type="hidden" value="sp_update" name="action">
                 <input type="hidden" value="dm_cloud_option_group" name="option_page">
                 <input type="hidden" value="front" name="_front_flag">
                 <table class="form-table">
                    <tbody>
                       <tr>
                          <th scope="row">UserID:</th>
                          <td><input type="text" value="" name="dm_cloud_option_name[cloud_user_id_number]" size="40" id="cloud_user_id_number"></td>
                       </tr>
                       <tr>
                          <th scope="row">APIKey:</th>
                          <td><input type="text" value="" name="dm_cloud_option_name[cloud_api_key]" size="40" id="cloud_api_key"></td>
                       </tr>
                    </tbody>
                 </table>
                 <div id="message"></div>
                 <p class="submit"><input type="submit" value="Connect my account" class="button button-primary" id="submit" name="submit"></p>
              </form>
           </div>
        </div>
     </div>

      <?php   endif;  }
}