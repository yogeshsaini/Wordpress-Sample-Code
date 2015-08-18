 <?php
   global $dm_session_store;
   $credents     = $dm_session_store;
   $selected     = isset( $_REQUEST['filter'] ) && !empty( $_REQUEST['filter'] ) ? $_REQUEST['filter'] : 'me';
   $video_status = ( isset( $_REQUEST['status'] ) || !empty( $_REQUEST['status'] ) ) ? $_REQUEST['status'] : 'all';
   $search_title = ( isset( $_REQUEST['dm_video_title'] ) && !empty( $_REQUEST['dm_video_title'] ) ) ? $_REQUEST['dm_video_title'] : '';
   @session_start();
   if ( isset( $_SESSION['dm_success'] ) ) {
                   $message = '<div class="dm-success-message"><span></span>' . $_SESSION['dm_success'] . '</div>';
                   unset( $_SESSION['dm_success'] );
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
            <li><a href="<?php echo SAMPLE_ADMIN_URL; ?>admin.php?page=dm-video-gallery" class="active-select"><img src="<?php echo SAMPLE_URL; ?>/assets/img/daily_tab_head.png" alt="Sample" /></a></li>
            <li><a href="<?php echo SAMPLE_ADMIN_URL; ?>admin.php?page=video-gallery-page"><img src="<?php echo SAMPLE_URL; ?>/assets/img/dynaamo_tab_head.png" alt="Sample Cloud" /></a></li>

         </ul>
      </div>
   </div>
   <div id="sample_cloud_simple_div" class="infomation_form">
      <div class="wrap">
         <?php
         if (isset($credents) && !empty($credents)) {
            $dmVideos = $this->sp_get_sample_videos($selected, $search_title, $video_status);
            //echo "<pre>"; print_r($dmVideos);
            //if (count($dmVideos) == 0) {
            //   $dmVideos = $this->sp_get_sample_videos('all', $search_title, $video_status);
            //}

         ?>
         <div id="sample-cloud">
            <div class="search-form">
               <div class="overlay"></div>
               <div class="loading-image-container"><img alt="ajax-loading" class="ajax-loading-img" src="<?php echo SAMPLE_URL; ?>/assets/img/495.GIF" /></div>
               <form method="get" action="">
                  <input type="hidden" value="<?=$_REQUEST['page'];?>" name="page">
                  <input id="dm-video-title" type="text" name="dm_video_title" value="<?php if(isset($_REQUEST['dm_video_title'])) {print $_REQUEST['dm_video_title'];}?>" placeholder="Search my videos"/>
                  <input type="button" value="Search" onclick="this.form.submit();">

               <div class="dsl-filter" name="">
                  <select class="private-public" name="status" onchange="this.form.submit();">
                     <option value="all" <?php if (isset($_REQUEST['status']) && $_REQUEST['status'] == "all"): print 'SELECTED="SELECTED"'; endif;?>>All</option>
                     <option value="0" <?php if (isset($_REQUEST['status']) && $_REQUEST['status'] === "0"): print 'SELECTED="SELECTED"'; endif;?>>Public only</option>
                     <option value="1" <?php if (isset($_REQUEST['status']) && $_REQUEST['status'] == "1"): print 'SELECTED="SELECTED"'; endif;?>>Private only</option>
                  </select>
               </div>

               <div class="dsl-sort">
                     <label>Sort by</label>
                     <select name="filter" id="dsl-filter" onchange="this.form.submit();">
                     <?php

                        $options = array('me'=>'Most Recent','commented'=>'Most Commented','rated'=>'Most Liked','visited'=>'Most Viewed');
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

               //echo "<pre>"; print_r($dmVideos);
               if ($search_title != '') :
                     echo '<div class="search-result-head"><div class="back-link"><a href="'.SAMPLE_ADMIN_URL.'admin.php?page=dm-video-gallery"> << Back to list</a></div><div class="result-count">Results for <i>'.$search_title.'</i></div></div>';
                  endif;
               ?>
            </div>
            <div id="samplecloud-display-section">

               <div class="dsl-pager">
                  <span class="paging-display">
                     <?php
                        if (isset($_REQUEST['pageno'])) {
                           $pageno = $_REQUEST['pageno'];
                        } else {
                           $pageno = 1;
                        }
                        $next = '';
                        $previous = '';

                        if ($pageno > 1):
                        $previous = $pageno - 1;
                        echo '<a class="previous" href="'.SAMPLE_ADMIN_URL.'admin.php?page=dm-video-gallery&pageno='.$previous.'&status='.$video_status.'&filter='.$selected.'&dm_video_title='.$search_title.'">Previous</a>';
                        endif;
                        if (isset($dmVideos['has_more']) && $dmVideos['has_more'] == 1):
                        $next = $pageno + 1;
                        echo '<a class="next" href="'.SAMPLE_ADMIN_URL.'admin.php?page=dm-video-gallery&pageno='.$next.'&status='.$video_status.'&filter='.$selected.'&dm_video_title='.$search_title.'">Next</a>';
                        endif;
                     ?>
                  </span>
               </div>

               <?php
               if (isset($dmVideos['videos']) && count($dmVideos['videos']) > 0) {
               ?>
               <table class = "video-gallery-container" cellpadding = "0" cellspacing = "0">
                  <?php
                        foreach ($dmVideos['videos'] as $video) {
                        $media_image_url = ($video['thumbnail_url']) ? $video['thumbnail_url'] : SAMPLE_URL . '/assets/img/no_files_found.jpg';
                        if (strlen($video['title']) > 167) {
                           $title = substr($video['title'], 0, 167) . '...';
                        } else {
                           $title = $video['title'];
                        }

                        $tags = (count($video['tags']) > 0 && isset($video['tags']))?implode(", ", $video['tags']):'';
                        $channel = (!empty($video['channel.name']))?$video['channel.name'] . ' - ' : '';

                        $status = (isset($video['private']) && $video['private'] == 1)?"Private":"Public";
                        $overlayClasspp = (isset($status) && $status == 'Private')?'<div class="overlay-container"><div class="privateOverlay"></div></div>':'';
                        $publish = (isset($video['published']) && $video['published'] == 1)?"published":"unpublished";
                        $overlayClasspu = (isset($publish) && $publish == 'unpublished')?'<div class="private-container"><div class="unpublishedOverlay"></div></div>':'';
                  ?>
                  <tr class="dm-gallery-rows">
                     <td class="image"><?php print $overlayClasspp; ?> <?php print $overlayClasspu; ?><img title="<?php print $video['title'];?>" alt="<?php print $video['embed_url'] ;?>" class="video-thumbnail" src="<?php print $media_image_url ;?>" alt="" /><span class="dm-play-time"><?php print $video['duration'];?></span></td>
                     <td class="Vtitle">
                        <div class="dm-title"><?php print $title;?></div>
                        <div class="private-status"><?php if ($status == 'Private'):print '- '.$status;endif;?></div>
                        <span class="desc"><?php print $video['description'];?></span>
                        <div class="view-container">
                           <span class="tags"><?php print $channel;?></span>
                           <span class="views italic"><?php print $video['views_total'];?> views</span>
                           <?php if ($publish == 'unpublished'):?>
                              <span class="unpublished-tooltip italic">, unpublished <span class="qus_mark tooltip"><span><img class="callout" src="<?php print SAMPLE_URL; ?>/assets/img/tool-tip-arrow.png" />A video can only be published when it has both a title and a channel assigned to it.</span></span></span>
                           <?php endif; ?>
                           <span class="video-status"><?php print $status; ?></span>
                        </div>
                     <div class="hide-option">
                         <a class="edit-trigger" onclick="edit_sample_video('<?php print $video['id'] ;?>');" href="javascript:void(0);">Edit</a>
                         <a class="trash-trigger" href="javascript:void(0);">Trash</a>
                             <div class="confirm-box">
                                 <div class="head"><span class="arrow"></span>Delete this video?</div>
                                 <div class="message">This video will be deleted from your Sample.com account.</div>
                                     <a class="keep-it" href="javascript:void(0);">No, keep it</a>
                                     <a rel="<?php print $video['id'];?>" class="delete-it" href="javascript:void(0);">Yes, delete</a>
                                 </div>
                         <a class="view-trigger" href="javascript:void(0);">view</a>
                     </div>
                     </td>
                     <td class="Vdate"><span class="date"><?php print date('M d, Y', $video['created_time']);?></span><span class="tag"><?php print $tags;?></span></td>
                  </tr>
                  <?php } ?>
               </table>
               <div class="dm-paging">
                <div class="dsl-pager">
                  <span class="paging-display">
                     <?php
                     if ($pageno > 1):
                        $previous = $pageno - 1;
                        echo '<a class="previous" href="'.SAMPLE_ADMIN_URL.'admin.php?page=dm-video-gallery&pageno='.$previous.'&status='.$video_status.'&filter='.$selected.'&dm_video_title='.$search_title.'">Previous</a>';
                        endif;
                        if (isset($dmVideos['has_more']) && $dmVideos['has_more'] == 1):
                        $next = $pageno + 1;
                        echo '<a class="next" href="'.SAMPLE_ADMIN_URL.'admin.php?page=dm-video-gallery&pageno='.$next.'&status='.$video_status.'&filter='.$selected.'&dm_video_title='.$search_title.'">Next</a>';
                        endif;
                     ?>
                  </span>
                </div>
               </div>
               <?php
                  } else if($search_title != '') {
                     echo '<div class="no-result-main">
                     <div class="inner"></div>
                     <div class="msg-line-one">No videos found for <span class="italic">'. $search_title .'.</span><span class="new-search">Try a new search.</span></div>
                  </div>'; }
                  ?>
            </div>
            <a href="#dm-cloud-edit-form" id="editpopup"></a>
            <div class="dmc-edit-form-main">
               <div id="dm-cloud-edit-form" class="dm-cloud-edit-form"></div>
            </div>
         </div>
         <?php } else {
            echo '<div class="dmc-not-auth">
               <div class="icon"></div>
               <div class="msg"><p>You are not connected to an account on Sample.</p><p>In order to see your videos here, go to the plug-in setting to connect your Sample account.</p></div>
               <div class="link"><a href="'. SAMPLE_ADMIN_URL .'admin.php?page=dm-admin-setting">Go to Settings</a></div>
            </div>';
            } ?>
      </div>
   </div>
</div>