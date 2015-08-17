jQuery(document).ready(function() {
    //Getting Post Title
    var post_title = jQuery('#post').find('input[name="post_title"]').val();
    if (post_title != '') {
        jQuery("#sample-video-title").val(post_title);
    }
    //Logic to check DM and DMC connections
    if (ajax_object.auth_status == 'BOTH_DISCONNECTED') {
        var str = '';
        str += '<div class="not-auth-main">';
        str += '<div class="icon"></div>';
        str += '<div class="msg"><p>You are not connected to an account on Sample.com and Dynaamo SmartCloud.</p><p>In order to see your videos here, go to the plug-in settings to connect your Sample account(s).</p></div>';
        str += '<div class="link"><a href="' + ajax_object.connect_url + '">Settings</a></div>';
        str += '</div>';
        jQuery("div.metabox-wrap").html(str);
    } else if (ajax_object.auth_status == 'ONLY_DMC_CONNECTED') {
        var str = '';
        str += '<div class="not-auth-main">';
        str += '<div class="icon"></div>';
        str += '<div class="msg"><p>You are not connected to an account on Sample.com.</p><p>In order to see your videos here, go to the plug-in settings to connect your Sample account(s).</p></div>';
        str += '<div class="link"><a href="' + ajax_object.connect_url + '">Settings</a></div>';
        str += '</div>';
        jQuery("#video_group").hide();
        jQuery("#sample_div").html(str);
    } else if (ajax_object.auth_status == 'ONLY_DM_CONNECTED') {
        jQuery("#video_group").remove();
    }
    //Logic to handling sidebar tabs
    jQuery('#metabox-tabs a').click(function() {
        var divid = jQuery(this).attr('rel');
        jQuery('.metabox-data').hide();
        jQuery('#metabox-tabs a').removeClass('active-select');
        jQuery(this).addClass('active-select');
        jQuery('#' + divid).show();
        if (divid != '') {
            var group = jQuery('#video_group option:selected').val();
            if (group == 'dm_cloud') {
                getMetaBoxTabContent(divid, 'dm_cloud');
            } else {
                getMetaBoxTabContent(divid, 'dm');
            }
        }
        return false;
    });
    jQuery('#metabox-tabs a').eq(0).trigger('click');
    //Logic to show and hide popup on hovering thumbnails showing in sidebar.
    jQuery('#sample_div_callback, #my_video_div_callback').hoverIntent({
        over: function() {
            var string = '';
            jQuery(this).find('img.dm-video-thumbnail').fadeTo(200, 0.25).end();
            var embed_url = jQuery(this).find('img.dm-video-thumbnail').attr('alt');
            var title = jQuery(this).find('img.dm-video-thumbnail').attr('title');
            var id = jQuery(this).find('img.dm-video-thumbnail').attr('id');
            var views = jQuery(this).find('span#total-views').text();
            var desc = jQuery(this).find('div.meta-desc').text();
            var videoURL = jQuery(this).find('div.video-url').text();
            var authorname = jQuery(this).find('span#author').text();
            var avatar = jQuery(this).find('span#avatar').text();
            string += '<iframe width="236" height="200" frameborder="0" scrolling="no" src="' + embed_url + '"></iframe>';
            string += '<span class="popbox-title">' + title + '</span>';
            string += '<div class="video-info">';
            if (avatar) {
                var avatartdiv = '<img src="' + avatar + '" class="avatar">';
            } else {
                var avatartdiv = '';
            }
            if (authorname) {
                string += '<span class="author">' + avatartdiv + '' + authorname + '</span>';
            }
            string += '<span class="views">' + views + '</span>';
            if (desc != null) {
                string += '<span class="description">' + desc + '</span>';
            }
            string += '</div>';
            string += '<div class="button-container">';
            string += '<a class="meta-insert" href="javascript:void(0);" onclick="insertIntoContent(\'' + id + '\', \'' + embed_url + '\');">Insert video into post</a>';
            string += '<a id="cpoy_' + id + '" class="meta-copy" rel="' + id + '" href="javascript:void(0);">Copy URL</a>';
            string += '<div id="video-url">' + embed_url + '</div>';
            string += '</div>';
            jQuery(this).find("#replace-container").html(string);
            jQuery(this).find('span.popbox').fadeIn();
            var copy_sel = $('.button-container a.meta-copy');
            copy_sel.clipboard({
                path: ajax_object.sample_url + '/js/clipboard/jquery.clipboard.swf',
                copy: function() {
                    var this_sel = $(this);
                    return this_sel.next().html();
                }
            });
        },
        out: function() {
            jQuery(this).find('img.dm-video-thumbnail').fadeTo(200, 1).end();
            jQuery(this).find('span.popbox').fadeOut();
        },
        selector: 'li',
        timeout: 100
    });
    //Logic to block refresh page when hit enter for searching Sample videos.
    jQuery('#sample-video-title').keypress(function(event) {
        if (event.keyCode == 13) {
            get_dm_videos_by_title('', jQuery(this).val());
            event.preventDefault();
            return false;
        }
    });
    //Logic to block refresh page when hit enter for searching Sample Cloud videos.
    jQuery('#my-video-title').keypress(function(event) {
        var group = jQuery('#video_group option:selected').val();
        if (event.keyCode == 13) {
            var searchTitle = jQuery(this).val();
            if (searchTitle != '') {
                if (group == 'dm_cloud') {
                    renderDMCloudVideos('', searchTitle);
                } else {
                    renderMyDMVideos('', searchTitle);
                }
            }
            event.preventDefault();
            return false;
        }
    });
    //Logic to fire event for Sample cloud videos searching
    jQuery("#my-video-title").bind('blur', function() {
        var group = jQuery('#video_group option:selected').val();
        var searchTitle = jQuery(this).val();
        if (searchTitle != '') {
            if (group == 'dm_cloud') {
                renderDMCloudVideos('', searchTitle);
            } else {
                renderMyDMVideos('', searchTitle);
            }
        }
    });
});
//Logic to pass the request to appropriate method on certain actions.
function getMetaBoxTabContent(divId, group) {
    var action = '';
    if (divId == 'sample_div' && (ajax_object.auth_status == 'BOTH_CONNECTED' || ajax_object.auth_status == 'ONLY_DM_CONNECTED')) {
        renderDMVideos('');
    } else if (divId == 'my_video_div' && group == 'dm_cloud' && (ajax_object.auth_status == 'BOTH_CONNECTED' || ajax_object.auth_status == 'ONLY_DMC_CONNECTED')) {
        var searchTitle = jQuery("#my-video-title").val();
        if (!searchTitle) {
            searchTitle = 'notitle'
        }
        renderDMCloudVideos('', searchTitle);
    } else if (divId == 'my_video_div' && group == 'dm' && (ajax_object.auth_status == 'BOTH_CONNECTED' || ajax_object.auth_status == 'ONLY_DM_CONNECTED')) {
        var searchTitle = jQuery("#my-video-title").val();
        if (!searchTitle) {
            searchTitle = 'notitle'
        }
        renderMyDMVideos('', searchTitle);
    } else if ((ajax_object.auth_status == 'BOTH_CONNECTED' || ajax_object.auth_status == 'ONLY_DM_CONNECTED')) {
        renderDMVideos('');
    }
}
//Method for rendering SAMPLE CLOUD VIDEOS

function renderDMCloudVideos(pn, searchTitle) {
    jQuery("#my_video_div_callback").html('');
    jQuery("#mydm_paging_callback").html('');
    jQuery(".metabox-loading-image-container").show();
    var pageno;
    if (pn == '') {
        pageno = 1;
    } else {
        pageno = pn;
    }
    var data = {
        action: 'get_my_dmc_video_metabox_tab_videos',
        pagenumber: pageno,
        title: searchTitle
    };
    jQuery.post(ajax_object.ajax_url, data, function(response) {
        var data = jQuery.parseJSON(response);
        var stringelement = '';
        var paging = '';
        if ((response == "[]" || !response) && (searchTitle == "notitle")) {
            stringelement += '<div class="no-video-main">';
            stringelement += '<div class="icon"></div>';
            stringelement += '<div class="msg"><p>You have not uploaded any video yet.</p><p>Start uploading your videos now!</p></div>';
            stringelement += '<div class="link"><a href="' + ajax_object.upload_url + '">Upload videos</a></div>';
            stringelement += '</div>';
        } else {
            if (typeof data.videos != "undefined" && (data.videos instanceof Array)) {
                stringelement += '<ul>';
                for (var i = 0; i < data.videos.length; i++) {
                    if (data.videos[i].stream_url != '' && data.videos[i].stream_url != null) {
                        var thumburl = data.videos[i].stream_url;
                    } else {
                        var thumburl = ajax_object.sample_url + '/img/no_files_found.jpg';
                    }
                    stringelement += '<li>';
                    stringelement += '<div class="meta-image">';
                    stringelement += '<a href="javascript:void(0);" onclick="insertIntoContent(\'' + data.videos[i].media_id + '\', \'' + data.videos[i].embed_url + '\');">Insert</a>';
                    stringelement += '<img alt="' + data.videos[i].embed_url + '" id="' + data.videos[i].media_id + '" class="dm-video-thumbnail dmMetaThumb" src="' + thumburl + '" title="' + data.videos[i].title + '" />';
                    stringelement += '</div>';
                    stringelement += '<div class="video-info">';
                    stringelement += '<a href="#">' + (data.videos[i].title).substr(0, 60) + '</a>';
                    stringelement += '<span class="views" id="total-views"> ' + data.videos[i].total_view + ' views</span>';
                    stringelement += '</div>';
                    stringelement += '<span class="popbox"><span id="replace-container"></span><span class="tooltip-arrow"></span></span>';
                    stringelement += '</li>';
                }
                stringelement += '</ul>';
            } else if (response.error) {
                txt = "There was an error on this page.\n\n";
                txt += "Error description: " + response.error.message + "\n\n";
                txt += "Click OK to continue.\n\n";
                alert(txt);
            } else if (searchTitle != "notitle") {
                stringelement += '<div class="no-result-main">';
                stringelement += '<div class="inner"></div>';
                stringelement += '<div class="msg-line-one">No videos found for <span class="italic">' + searchTitle + '</span>.<span>Try a new search.</span></div>';
                stringelement += '</div>';
            }
        }
        paging += '<div class="paging">';
        if (pageno > 1) {
            var previous = parseInt(pageno) - parseInt(1);
            paging += '<a class="previous" href="javascript:void(0);" onclick="renderDMCloudVideos(\'' + previous + '\', \'' + searchTitle + '\');">Previous</a>';
        }
        if (data.total_pages > pageno) {
            var next = parseInt(pageno) + parseInt(1);
            paging += '<a class="next" href="javascript:void(0);" onclick="renderDMCloudVideos(\'' + next + '\', \'' + searchTitle + '\');">Next</a>';
        }
        paging += '</div>';
        stringelement += paging;
        jQuery(".metabox-loading-image-container").hide();
        jQuery("#my_video_div_callback").html(stringelement);
    });
}
//Method for rendering SAMPLE ALL VIDEOS

function renderDMVideos(pn) {
    //checking that Post title has content or not. If it has content the displaying default videos on the bases of Post Title.
    var post_title = jQuery('#post').find('input[name="post_title"]').val();

    if (typeof(post_title) !== "undefined" && post_title !== null) {
        get_dm_videos_by_title('', post_title);
        return false;
    }
    jQuery("#sample_div_callback").html('');
    jQuery("#dm_paging_callback").html('');
    jQuery(".metabox-loading-image-container").show();
    var pageno;
    if (pn == '') {
        pageno = 1;
    } else {
        pageno = pn;
    }
    var for_author_name;
    DM.api('/videos?filters=creative-official', {
        page: pageno,
        limit: 5,
        fields: "id,title,url,embed_url,thumbnail_url,views_total,description,owner.screenname,owner.avatar_25_url"
            // country: countryCode
    }, function(response) {
        for_author_name = (JSON.parse(JSON.stringify(response)));
        var stringelement = '';
        var paging = '';
        if ((response.list.length) > 0) {
            stringelement = '<ul>';
            for (var i = 0; i < response.list.length; i++) {
                var description = response.list[i].description;
                if (description.length > 160) {
                    description = (response.list[i].description).substr(0, 160) + '...';
                }
                if (response.list[i].thumbnail_url != '' && response.list[i].thumbnail_url != null) {
                    var thumburl = response.list[i].thumbnail_url;
                } else {
                    var thumburl = ajax_object.sample_url + '/img/no_files_found.jpg';
                }
                stringelement += '<li>';
                stringelement += '<div class="meta-image">';
                stringelement += '<a href="javascript:void(0);" onclick="insertIntoContent(\'' + response.list[i].id + '\', \'' + response.list[i].embed_url + '\');">Insert</a>';
                stringelement += '<img alt="' + response.list[i].embed_url + '" id="' + response.list[i].id + '" class="dm-video-thumbnail dmMetaThumb" src="' + thumburl + '" title="' + response.list[i].title + '" />';
                stringelement += '</div>';
                stringelement += '<div class="video-info">';
                stringelement += '<a href="#">' + (response.list[i].title).substr(0, 37) + '...</a>';
                stringelement += '<span id="avatar">' + for_author_name["list"][i]["owner.avatar_25_url"] + '</span>';
                stringelement += '<span id="author">By ' + for_author_name["list"][i]["owner.screenname"] + '</span>';
                stringelement += '<span class="views" id="total-views"> ' + response.list[i].views_total + ' views</span> ';
                stringelement += '</div>';
                stringelement += '<span class="popbox"><span id="replace-container"></span><span class="tooltip-arrow"></span></span>';
                stringelement += '<div class="meta-desc">' + description + '</div>';
                stringelement += '<div class="video-url">' + response.list[i].url + '</div>';
                stringelement += '</li>';
            }
            stringelement += '</ul>';
        } else if (response.error) {
            txt = "There was an error on this page.\n\n";
            txt += "Error description: " + response.error.message + "\n\n";
            txt += "Click OK to continue.\n\n";
            alert(txt);
        } else {
            stringelement += '<div class="no-result-main">';
            stringelement += '<div class="inner"></div>';
            stringelement += '<div class="msg">No videos found.</div>';
            stringelement += '</div>';
        }
        paging += '<div class="paging">';
        if (pageno > 1) {
            var previous = parseInt(pageno) - parseInt(1);
            paging += '<a class="previous" href="javascript:void(0);" onclick="renderDMVideos(\'' + previous + '\');">Previous</a>';
        }
        if (response.has_more) {
            var next = parseInt(pageno) + parseInt(1);
            paging += '<a class="next" href="javascript:void(0);" onclick="renderDMVideos(\'' + next + '\');">Next</a>';
        }
        paging += '</div>';
        stringelement += paging;
        jQuery("#sample_div_callback").html(stringelement);
        jQuery(".metabox-loading-image-container").hide();
    });
    // });
}
//Render DM videos by title

function get_dm_videos_by_title(pn, title) {
    if (title != "") {
        jQuery("#dm_paging_callback").html('');
        jQuery("#sample_div_callback").html('');
        jQuery(".metabox-loading-image-container").show();
        var pageno;
        if (pn == '') {
            pageno = 1;
        } else {
            pageno = pn;
        }
        //  jQuery.getJSON('http://freegeoip.net/json/', function (location) {
        // var countryCode = location.country_code.toLowerCase();
        DM.api('/videos?sort=relevance', {
            page: pageno,
            limit: 5,
            search: title,
            fields: "id,title,embed_url,thumbnail_url,views_total,description,owner.screenname,owner.avatar_25_url"
                //country: countryCode
        }, function(response) {
            var stringelement = '';
            var paging = '';
            for_author_name = (JSON.parse(JSON.stringify(response)));
            if ((response.list.length) > 0) {
                var x = 0
                stringelement = '<ul>';
                for (var i = 0; i < response.list.length; i++) {
                    var description = response.list[i].description;
                    if (description.length > 160) {
                        description = (response.list[i].description).substr(0, 160) + '...';
                    } else {
                        description = '';
                    }
                    if (response.list[i].thumbnail_url != '' && response.list[i].thumbnail_url != null) {
                        var thumburl = response.list[i].thumbnail_url;
                    } else {
                        var thumburl = ajax_object.sample_url + '/img/no_files_found.jpg';
                    }
                    stringelement += '<li>';
                    stringelement += '<div class="meta-image">';
                    stringelement += '<a href="javascript:void(0);" onclick="insertIntoContent(\'' + response.list[i].id + '\', \'' + response.list[i].embed_url + '\');">Insert</a>';
                    stringelement += '<img alt="' + response.list[i].embed_url + '" id="' + response.list[i].id + '" class="dm-video-thumbnail dmMetaThumb" src="' + thumburl + '" title="' + response.list[i].title + '" />';
                    stringelement += '</div>';
                    stringelement += '<div class="video-info">';
                    stringelement += '<a href="#">' + (response.list[i].title).substr(0, 37) + '...</a>';
                    stringelement += '<span id="avatar">' + for_author_name["list"][i]["owner.avatar_25_url"] + '</span>';
                    stringelement += '<span id="author">By ' + for_author_name["list"][i]["owner.screenname"] + '</span>';
                    stringelement += '<span class="views" id="total-views"> ' + response.list[i].views_total + ' views</span>';
                    stringelement += '</div>';
                    stringelement += '<span class="popbox"><span id="replace-container"></span><span class="tooltip-arrow"></span></span>';
                    stringelement += '<div class="meta-desc">' + description + '</div>';
                    stringelement += '</li>';
                }
                stringelement += '</ul>';
            } else {
                stringelement += '<div class="no-result-main">';
                stringelement += '<div class="inner"></div>';
                stringelement += '<div class="msg-line-one">No videos found for <i>' + title + '</i>.<span>Try a new search.</span></div>';
                stringelement += '</div>';
            }
            paging += '<div class="paging">';
            if (pageno > 1) {
                var previous = parseInt(pageno) - parseInt(1);
                paging += '<a class="previous" href="javascript:void(0);" onclick="get_dm_videos_by_title(\'' + previous + '\', \'' + title + '\');">Previous</a>';
            }
            if (response.has_more) {
                var next = parseInt(pageno) + parseInt(1);
                paging += '<a class="next" href="javascript:void(0);" onclick="get_dm_videos_by_title(\'' + next + '\', \'' + title + '\');">Next</a>';
            }
            paging += '</div>';
            stringelement += paging
            jQuery("#sample_div_callback").html(stringelement);
            jQuery(".metabox-loading-image-container").hide();
        });
        // });
    } else if (title == 'Search') {
        renderDMVideos('');
    }
}
//Method for rendering SAMPLE MY VIDEOS

function renderMyDMVideos(pn, searchTitle) {
    var pageno;
    var for_author_name;
    if (pn == '') {
        pageno = 1;
    } else {
        pageno = pn;
    }
    jQuery("#mydm_paging_callback").html('');
    jQuery("#my_video_div_callback").html('');
    jQuery(".metabox-loading-image-container").show();
    var data = {
        action: 'get_my_dm_video_metabox_tab_videos',
        pagenumber: pageno,
        title: searchTitle
    };
    jQuery.post(ajax_object.ajax_url, data, function(response) {
        //var for_author_name = (jQuery.parseJSON(JSON.stringify(response)));
        var data = JSON.parse(JSON.stringify(response));
        var stringelement = '';
        var paging = '';
        if ((response == "[]" || !response) && (searchTitle == "notitle")) {
            jQuery("#my-video-title").hide();
            stringelement += '<div class="no-video-main">';
            stringelement += '<div class="icon"></div>';
            stringelement += '<div class="msg"><p>You have not uploaded any video yet.</p><p>Start uploading your videos now!</p></div>';
            stringelement += '<div class="link"><a href="' + ajax_object.upload_url + '">Upload videos</a></div>';
            stringelement += '</div>';
        } else {
            if (typeof data.videos != "undefined" && (data.videos instanceof Array)) {
                stringelement += '<ul>';
                for (var i = 0; i < data.videos.length; i++) {
                    var description = data.videos[i].description;
                    if (description != null && description.length > 160) {
                        description = (data.videos[i].description).substr(0, 160) + '...';
                    } else {
                        description = '';
                    }
                    if (data.videos[i].thumbnail_url != '' && data.videos[i].thumbnail_url != null) {
                        var thumburl = data.videos[i].thumbnail_url;
                    } else {
                        var thumburl = ajax_object.sample_url + '/img/no_files_found.jpg';
                    }
                    stringelement += '<li>';
                    stringelement += '<div class="meta-image">';
                    stringelement += '<a href="javascript:void(0);" onclick="insertIntoContent(\'' + data.videos[i].id + '\', \'' + data.videos[i].embed_url + '\');">Insert</a>';
                    stringelement += '<img alt="' + data.videos[i].embed_url + '" id="' + data.videos[i].id + '" class="dm-video-thumbnail dmMetaThumb" src="' + thumburl + '" title="' + data.videos[i].title + '" />';
                    stringelement += '</div>';
                    stringelement += '<div class="video-info">';
                    stringelement += '<a href="#">' + (data.videos[i].title).substr(0, 30) + '</a>';
                    stringelement += '<span class="views" id="total-views"> ' + data.videos[i].views_total + ' views</span>';
                    stringelement += '</div>';
                    stringelement += '<span class="popbox"><span id="replace-container"></span><span class="tooltip-arrow"></span></span>';
                    stringelement += '<div class="meta-desc">' + description + '</div>';
                    stringelement += '</li>';
                }
                stringelement += '</ul>';
            } else if (response.error) {
                txt = "There was an error on this page.\n\n";
                txt += "Error description: " + response.error.message + "\n\n";
                txt += "Click OK to continue.\n\n";
                alert(txt);
            } else if (searchTitle != "notitle") {
                stringelement += '<div class="no-result-main">';
                stringelement += '<div class="inner"></div>';
                stringelement += '<div class="msg-line-one">No videos found for <span class="italic">' + searchTitle + '</span>.<span>Try a new search.</span></div>';
                stringelement += '</div>';
            }
        }
        paging += '<div class="paging">';
        if (pageno > 1) {
            var previous = parseInt(pageno) - parseInt(1);
            paging += '<a class="previous" href="javascript:void(0);" onclick="renderMyDMVideos(\'' + previous + '\', \'' + searchTitle + '\');">Previous</a>';
        }
        if (data.has_more) {
            var next = parseInt(pageno) + parseInt(1);
            paging += '<a class="next" href="javascript:void(0);" onclick="renderMyDMVideos(\'' + next + '\', \'' + searchTitle + '\');">Next</a>';
        }
        paging += '</div>';
        stringelement += paging;
        jQuery(".metabox-loading-image-container").hide();
        jQuery("#my_video_div_callback").html(stringelement);
    });
}

function get_videos_by_group(option) {
    var divId = 'my_video_div';
    jQuery("#my-video-title").show();
    var post_title = jQuery('#post').find('input[name="post_title"]').val();
    if (post_title == '') {
        jQuery("#my-video-title").val('');
    }
    getMetaBoxTabContent(divId, option);
}
//Method for inserting Sample cloud short code into textarea

function insertIntoContent(Id, media_url) {
    var width = 300;
    var height = 250;
    var html = '[dmvideo id="' + Id + '" media_url="' + media_url + '" width="' + width + '" height="' + height + '"]'; //jQuery("#"+mediaId+"-video-iframe").html();
    var ed = 'content';
    window.send_to_editor(html);
    window.tb_remove();
    jQuery(".popbox").hide();
}

function insertAtCaret(areaId, text) {
    var txtarea = document.getElementById(areaId);
    var scrollPos = txtarea.scrollTop;
    var strPos = 0;
    var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? "ff" : (document.selection ? "ie" : false));
    if (br == "ie") {
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart('character', -txtarea.value.length);
        strPos = range.text.length;
    } else if (br == "ff") strPos = txtarea.selectionStart;
    var front = (txtarea.value).substring(0, strPos);
    var back = (txtarea.value).substring(strPos, txtarea.value.length);
    txtarea.value = front + text + back;
    strPos = strPos + text.length;
    if (br == "ie") {
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart('character', -txtarea.value.length);
        range.moveStart('character', strPos);
        range.moveEnd('character', 0);
        range.select();
    } else if (br == "ff") {
        txtarea.selectionStart = strPos;
        txtarea.selectionEnd = strPos;
        txtarea.focus();
    }
    txtarea.scrollTop = scrollPos;
}