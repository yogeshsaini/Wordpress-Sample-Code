<?php
global $user_meta;
$credents = $user_meta;
$selected = isset($_GET['filter']) && !empty($_GET['filter']) ? $_GET['filter'] : '-created';
$search_title = (isset($_REQUEST['dmc_video_title']) && !empty($_REQUEST['dmc_video_title']))?$_REQUEST['dmc_video_title']:'';
@session_start();
if(isset($_SESSION['dmc_success']))
{
   $message = '<div class="dm-success-message"><span></span>'.$_SESSION['dmc_success'].'</div>';
   unset($_SESSION['dmc_success']);
}
?>
<div class="main-gallery-container dm-common">
    <?php if(isset($message)):echo $message;endif;?>
   <div id="header-container" class="wrap">
      <div class="head-upper">
      <div class="title">Media Gallery</div>
      <div class="upload-link"><a href="<?php echo SAMPLE_ADMIN_URL; ?>admin.php?page=video-upload">Upload videos</a></div>
      </div>
      <div id="tabs-navigation">
         <ul>
            <li><a href="<?php echo SAMPLE_ADMIN_URL; ?>admin.php?page=dm-video-gallery"><img src="<?php echo SAMPLE_URL; ?>/img/daily_tab_head.png" alt="Sample" /></a></li>
            <li><a href="<?php echo SAMPLE_ADMIN_URL; ?>admin.php?page=video-gallery-page" class="active-select"><img src="<?php echo SAMPLE_URL; ?>/img/dynaamo_tab_head.png" alt="Sample Cloud" /></a></li>

         </ul>
      </div>
   </div>
   <div id="sample_cloud_div" class="infomation_form">
      <div class="wrap">
         <?php

            if (isset($credents) && !empty($credents)) {
               $dmcVideos = $this->getSampleCloudVideos($search_title, $selected);
               if (count($dmcVideos) > 0 || $search_title != '') {
               ?>
             <div id="sample-cloud">
            <div class="search-form">
               <div class="overlay"></div>
               <div class="loading-image-container"><img alt="ajax-loading" class="ajax-loading-img" src="<?php echo SAMPLE_URL; ?>/img/495.GIF" /></div>
               <form class="search" id="dmc_filter" method="get" action="">
                  <input type="hidden" value="<?=$_REQUEST['page'];?>" name="page">
                  <input type="text" name="dmc_video_title" id="dmc-video-title" value="<?php if(isset($_REQUEST['dmc_video_title'])) {print $_REQUEST['dmc_video_title'];}?>" placeholder="Search my videos"/>
                  <input type="button" value="Search" onclick="this.form.submit();">
                  <div class="dsl-sort">
                        <label>Sort by</label>
                        <select name="filter"  id="dsl-filter" onchange="this.form.submit();">
                        <?php
                           $options = array('-created'=>'Most Recent','meta.title'=>'A-Z','-meta.title'=>'Z-A');
                           foreach( $options as $key=>$option )
                                  {
                                      $select = $selected==$key ? ' selected' : null;
                                      $dropdown .= '<option value="'.$key.'"'.$select.'>'.$option.'</option>'."\n";
                                  }
                            print $dropdown;
                           ?>
                        </select>
                  </div>
               </form>
               <?php
               if ($search_title != '') {
                     if (isset($dmcVideos['total_record'])) {
                           $found = $dmcVideos['total_record'];
                        } else {
                           $found = 0;
                        }
                     echo '<div class="search-result-head"><div class="back-link"><a href="'.SAMPLE_ADMIN_URL.'admin.php?page=video-gallery-page"> << Back to list</a></div><div class="result-count">'.$found.' results for <i>'.$search_title.'</i></div></div>';
                  }
               ?>
            </div>
            <div id="samplecloud-display-section">
               <?php
               if (isset($dmcVideos['total_pages']) && count($dmcVideos['total_pages']) > 0) {
                     $nr = $dmcVideos['total_pages'];
                     $page_links = paginate_links( array(
                                 'base' => add_query_arg( 'pagenum', '%#%' ),
                                 'format' => '',
                                 'prev_text' => __( '&laquo;', 'text-domain' ),
                                 'next_text' => __( '&raquo;', 'text-domain' ),
                                 'total' => $nr,
                                 'current' => !empty($_GET['pagenum']) ? $_GET['pagenum'] : 1
                                 ) );

                     if ($page_links):
                        echo '<div class="dsl-pager"><span class="total-records italic">'.$dmcVideos['total_record'].' items</span><span class="paging-display">'. $page_links . '</span></div>';
                     endif;
               ?>

               <table width="100%" class="video-gallery-container" cellpadding="0" cellspacing="0">
                  <?php
                     foreach ($dmcVideos['videos'] as $video) {
                        $mediaImageURL = ($video['stream_url']) ? $video['stream_url'] : SAMPLE_URL . '/img/no_files_found.jpg';
                        if (strlen($video['title']) > 167) {
                          $title = substr(strip_tags($video['title']), 0, 167) . '...';
                        } else {
                          $title = strip_tags($video['title']);
                        }
                  ?>
                  <tr class="dmc-gallery-rows">
			    <td class="image"><img class="video-thumbnail" src="<?php print $mediaImageURL ;?>" alt="<?php print $video['embed_url'];?>" title="<?php print $video['title'];?>" /><span class="dmc-play-time"><?php print $video['duration'];?></span></td>
			    <td class="Vtitle"><div class="dmc-title"><?php print $title ;?></div><div class="views-container"><span class="italic"><?php print $video['total_view'];?> views</span></div>
                            <div class="hide-option">
                                <a class="dmc-edit-trigger" onclick="editDMCvideo('<?php print $video['media_id'] ;?>');" href="javascript:void(0);">Edit</a>
                                <a class="dmc-trash-trigger" href="javascript:void(0);">Trash</a>
                                    <div class="confirm-box">
                                        <div class="head"><span class="arrow"></span>Delete this video?</div>
                                        <div class="message">This video will be deleted from your Sample Cloud account.</div>
                                            <a class="dmc-keep-it" href="javascript:void(0);">No, keep it</a>
                                            <a rel="<?php print $video['media_id'] ;?>" class="dmc-delete-it" href="javascript:void(0);">Yes, delete</a>
                                        </div>
                                <a class="view-trigger" href="javascript:void(0);">view</a>
                            </div></td>
			    <td class="created"><?php print $video['created'];?><div class="dmc-keywords"><?php print $this->getDMCKeywords($video['media_id']);?></div></td>
			 </tr>
                  <?php } ?>
               </table>
               <?php } else {
                  echo '<div class="no-result-main">
                     <div class="inner"></div>
                     <div class="msg-line-one">No videos found for <span class="italic">'. $search_title .'.</span><span class="new-search">Try a new search.</span></div>
                  </div>';
                } ?>
               <?php
                  if (isset($dmcVideos['total_pages']) && count($dmcVideos['total_pages']) > 0 && $page_links):
                     echo '<div class="dm-paging"><div class="dsl-pager"><span class="paging-display"><span class="total-records italic">'.$dmcVideos['total_record'].' items</span>'. $page_links . '</span></div></div>';
                  endif;
               ?>
            </div>
            <a href="#dm-cloud-edit-form" id="editpopup"></a>
            <div class="dmc-edit-form-main">
               <div id="dm-cloud-edit-form" class="dm-cloud-edit-form dm_cloud_second"></div>
            </div>
         </div>
         <?php   }else if($search_title == '') { ?>
            <div class = "no-result-container">
               <div class="icon"></div>
               <div class="message"><p>You haven't uploaded any videos yet.</p><p>Start uploading your videos now!</p></div>
               <div class="page-link"><a href="#">Upload Videos</a></div>
               </div>
           <?php } }else { ?>
            <div class="dmc-not-auth">
               <div class="icon"></div>
               <div class="msg"><p>You are not connected to an account on Sample cloud.</p><p>In order to see your videos here, go to the plug-in setting to connect your Sample Cloud account.</p></div>
               <div class="link"><a href="<?php echo SAMPLE_ADMIN_URL; ?>admin.php?page=dm-admin-setting">Go to Settings</a></div>
            </div>
         <?php }
         ?>
      </div>
   </div>

</div>