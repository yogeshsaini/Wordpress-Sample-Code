<h2>ADD VIDEO(s) IN YOUR POST</h2>
<div class="postbox" id="sample_plugin_metabox">
   <div class="inside">
      <div class="main-metabox-container dm-common">
         <div class="metabox-overlay"></div>
         <div class="metabox-wrap">
            <div id="metabox-tabs">
               <ul>
                  <li><a href="#" rel="sample_div">Sample.com</a></li>
                  <li><a href="#" rel="my_video_div">My videos</a></li>
               </ul>
            </div>
            <div id="sample_div" class="metabox-data">
               <div class="search">
                  <input type="text" name="sample_video_title" id="sample-video-title" value="" onblur="getDMVideosByTitle('', this.value);" placeholder="Search videos on Dilymotion.com"/>
               </div>
               <div class="metabox-loading-image-container"><img alt="ajax-loading" class="ajax-loading-img" src="<?=SAMPLE_URL;?>/img/495.GIF" /></div>
               <div id="sample_div_callback" class="metabox-video-list"></div>
            </div>
            <div id="my_video_div" class="metabox-data">
               <div class="select-group">
                  <select name="video-group" id="video_group" onchange="getVideosByGroup(this.value)">
                     <option value="dm_cloud">Sample Cloud</option>
                     <option value="dm">Sample.com</option>
                  </select>
               </div>
               <div class="search group-search">
                  <input type="text" name="my_video_title" id="my-video-title" value="" placehold="Search my videos"/>
               </div>
               <div class="metabox-loading-image-container"><img alt="ajax-loading" class="ajax-loading-img" src="<?=SAMPLE_URL;?>/img/495.GIF" /></div>
               <div id="my_video_div_callback" class="metabox-video-list"></div>
            </div>
            <div class="clear"></div>
         </div>
      </div>
   </div>
</div>