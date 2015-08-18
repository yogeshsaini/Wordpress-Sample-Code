jQuery(document).ready(function() {
    jQuery("a#editpopup").fancybox({
        'hideOnOverlayClick': false,
        'onClosed': function() {
            //location.reload();
        }
    });
    jQuery(".toplevel_page_dm-admin-setting ul.wp-submenu li").each(function() {
        if ((jQuery(this).hasClass('current')) && (jQuery(this).find('a').attr('href') == 'admin.php?page=dm-video-gallery')) {
            jQuery(".toplevel_page_dm-admin-setting ul.wp-submenu li").eq(2).addClass('current');
            jQuery(".toplevel_page_dm-admin-setting ul.wp-submenu li").eq(2).find('a').addClass('current');
        }
    });
    /*
    The code below will write
    to a open sample cloud
    form in fancybox popup
    */
    jQuery("a#cloud_form_link").on("click", function(event) {
        event.preventDefault();
    }).fancybox();
    /*
    The code below will write
    to a open sample
    form in fancybox popup
    */
    jQuery("a#dm_auth_popup").on("click", function(event) {
        event.preventDefault();
    }).fancybox({
        onStart: function() {
            jQuery("form#sample_outh_form").fadeIn('slow');
            jQuery("div.create_account_desc").hide();
        }
    });
    jQuery("a#sub_link").on("click", function(event) {
        event.preventDefault();
    }).fancybox({
        onStart: function() {
            jQuery("form#sample_outh_form").hide();
            jQuery("div.create_account_desc").fadeIn('slow');
            //jQuery("a.hide_account_desc").removeClass("hide_account_desc");
        }
    });
    /*
    The code below will write
    to a open sample
    form in fancybox popup
    */
    jQuery("a.show_account_desc").on("click", function(event) {
        jQuery("form#sample_outh_form").hide();
        jQuery("div.create_account_desc").fadeIn('slow');
    });
    jQuery("a.hide_account_desc").on("click", function(event) {
        jQuery("div.create_account_desc").hide();
        jQuery("form#sample_outh_form").fadeIn('slow');
    });
    /*
    The code below will write
    to a open sample cloud
    form in fancybox popup
    */
    jQuery(".disconnect_wrapper").on("click", "a", function(event) {
        var CurRel = jQuery(this).attr("rel");
        jQuery.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: "action=sp_discconet_account&account_name=" + CurRel,
            success: function(data) {
                if (data.success) {
                    jQuery('#' + CurRel + '_throbber').addClass('displaynone');
                    setTimeout("location.href='" + ajax_object.connect_url + "';", 1000);
                    //jQuery("#message").html(data.success).slideDown();
                }
            },
            beforeSend: function(data) {
                jQuery('#' + CurRel + '_throbber').removeClass('displaynone');
            },
            error: function(data) {
                jQuery("#message").html(data).slideDown();
            }
        });
        event.preventDefault();
    });
    /*
    The code below will write
    to a open upload video
    form in fancybox popup
    */
    jQuery("a#videopop").fancybox({
        'width': '75%',
        'height': '75%',
        'autoScale': false,
        'transitionIn': 'none',
        'transitionOut': 'none',
        'type': 'iframe',
        'hideOnOverlayClick': false,
        'onClosed': function() {
            //location.reload();
        }
    });
    //Logic to display Delete confirm box on Sample Video Gallery
    jQuery(".trash-trigger").bind("click", function() {
        jQuery("div.confirm-box").hide();
        jQuery(this).closest("div").find("div.confirm-box").fadeIn();
    });
    //Logic to delete the Sample videos
    jQuery("a.delete-it").live("click", function() {
        var videoId = jQuery(this).attr('rel');
        var data = {
            action: 'delete_dm_video',
            Id: videoId
        };
        // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        jQuery.post(ajax_object.ajax_url, data, function(response) {
            //alert('Got this from the server: ' + response);
            location.reload();
        });
    });
    //Logic to display preview of Sample Cloud video when click on "view" link.
    jQuery(".view-trigger").bind("click", function() {
        jQuery(this).closest("tr.dm-gallery-rows").trigger("click");
    });
    //Logic to hide Delete confirm box of Sample Video Gallery when clicked on Keep-it link
    jQuery("a.keep-it").live("click", function() {
        jQuery("div.confirm-box").fadeOut();
    });
    //Logic to display Delete confirm box on Sample Cloud Video Gallery
    jQuery(".dmc-trash-trigger").live("click", function() {
        jQuery("div.confirm-box").hide();
        jQuery(this).closest("div").find("div.confirm-box").fadeIn();
    });
    //Logic to delete the Sample Cloud videos
    jQuery("a.dmc-delete-it").live("click", function() {
        var videoId = jQuery(this).attr('rel');
        var data = {
            action: 'delete_dm_cloud_records',
            mediaId: videoId
        };
        // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        jQuery.post(ajax_object.ajax_url, data, function(response) {
            //alert('Got this from the server: ' + response);
            location.reload();
        });
    });
    //Logic to display preview of Sample Cloud video when click on "view" link.
    jQuery(".view-trigger").bind("click", function() {
        jQuery(this).closest("tr.dmc-gallery-rows").trigger("click");
    });
    //Logic to hide Delete confirm box of Sample Cloud Video Gallery when clicked on Keep-it link
    jQuery("a.dmc-keep-it").live("click", function() {
        jQuery("div.confirm-box").fadeOut();
    });
    jQuery("#dm-video-private").live("click", function() {
        jQuery(this).closest("div.visibility-wrap").removeClass("blur");
        jQuery("div.visibility_public").addClass("blur");
    });
    jQuery("#dm-video-public").live("click", function() {
        jQuery(this).closest("div.visibility-wrap").removeClass("blur");
        jQuery("div.visibility_private").addClass("blur");
    });
    //Open confirmation popup for cloud registration
    jQuery("a#cloud_register_trigger").click(function() {
        var string = '<div class="cloud-register-main">';
        string += '<p>You are creating an account on Sample and on Dynaamo powered by Sample. Your videos will be managed by Dynaamo.</p>';
        string += '<div class="cloud-register-message"></div>';
        string += '<div class="cloud-register-link"><a href="javascript:void(0);">Ok, continue</a></div>';
        string += '</div>';
        jQuery.fancybox(string, {
            overlayShow: true,
            hideOnContentClick: false
        });
    });
    //Logic to create account on sample
    jQuery(".cloud-register-link a").live("click", function() {
        var data = {
            action: 'create_dm_cloud_account',
        };
        // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        jQuery.post(ajax_object.ajax_url, data, function(response) {
            if (response == 'SUCCESS') {
                jQuery("div.cloud-register-message").addClass('success');
                jQuery("div.cloud-register-message").html('Your account has been successfully created on Sample cloud.');
            } else if (response == 'FAILURE') {
                jQuery("div.cloud-register-message").addClass('error');
                jQuery("div.cloud-register-message").html('It seems some issue for creating account on sample cloud. May be entered email already registered with sample cloud.');
            }
            location.reload();
        });
    });
});
/*
The below code write
to save cloud form using ajax
*/
function cloud_settings_form_submit(formobj) {
    var form_data = jQuery("#cloud_settings_form").serialize();
    formobj.submit.disabled = true;
    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: form_data,
        success: function(data) {
            if (data.error) {
                formobj.submit.disabled = false;
                jQuery("#message").html(data.error).slideDown();
            } else if (data.success) {
                if (ajax_object.check_on_theme) {
                    setTimeout("location.href='" + ajax_object.front_page_url + "';", 2000);
                } else {
                    setTimeout("location.href='" + ajax_object.connect_url + "';", 2000);
                }
            }
        },
        beforeSend: function() {},
        error: function(data) {
            jQuery("#message").html(data).slideDown();
        }
    });
    return false;
}
/*
The below code write
to save publication form using ajax
*/

function publication_settings_form_submit(formobj) {
    var form_data = jQuery("#publication_settings_form").serialize();
    formobj.submit.disabled = true;
    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: form_data,
        success: function(data) {
            if (data.error) {
                formobj.submit.disabled = false;
                if (data.error == 'Please enter both fields values.') {
                    jQuery('#dm_channel').closest("tr").css('border', '2px solid red');
                    jQuery('#publisher_id').closest("tr").css('border', '2px solid red');
                } else if (data.error == 'Please select a channel.') {
                    jQuery('#dm_channel').closest("tr").css('border', '2px solid red');
                } else if (data.error == 'Please Enter Your Publisher Id.') {
                    jQuery('#publisher_id').closest("tr").css('border', '2px solid red');
                }
                jQuery("#publicationmessage").html(data.error).slideDown();
                formobj.submit.disabled = false;
            } else if (data.success) {
                formobj.submit.disabled = false;
                jQuery('#dm_channel').closest("tr").css('border', 'none');
                jQuery('#publisher_id').closest("tr").css('border', 'none');
                jQuery("#publicationmessage").addClass('success-msg').html(data.success).slideDown();
            }
        },
        beforeSend: function() {},
        error: function(data) {
            jQuery("#message").html(data).slideDown();
        }
    });
    return false;
}
/*
The below code write
to save sample form using ajax
*/

function sample_settings_form_submit(formobj) {
    var form_data = jQuery("#sample_outh_form").serialize();
    formobj.submit.disabled = true;
    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: form_data,
        success: function(data) {
            if (data.error) {
                formobj.submit.disabled = false;
                if (data.error == 'Please enter your api key.') {
                    $('#sample_apikey').closest("tr").css('border', '2px solid red');
                } else if (data.error == 'Please enter your secret key.') {
                    $('#sample_secretkey').closest("tr").css('border', '2px solid red');
                }
                jQuery("#sample_message").html(data.error).slideDown();
            } else if (data.success) {
                formobj.submit.disabled = false;
                jQuery('#sample_apikey').closest("tr").css('border', 'none');
                jQuery('#sample_secretkey').closest("tr").css('border', 'none');
                //jQuery("#sample_message").addClass('success-msg').html(data.success).slideDown();
                location.href = data.success;
            }
        },
        beforeSend: function() {},
        error: function(data) {
            jQuery("#message").html(data).slideDown();
        }
    });
    return false;
}
// jQuery function to edit Sample cloud video

function editDMCvideo(mediaId) {
    var CurPage = getQueryStringByName('page');
    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        dataType: 'html',
        data: 'action=sp_edit_dm_cloud_records&mediaId=' + mediaId + '&curpage=' + CurPage,
        success: function(data) {
            //jQuery("div.overlay").hide();
            jQuery("div.loading-image-container").fadeOut();
            jQuery(".dm-cloud-edit-form").html(data);
            jQuery("a#editpopup").trigger("click");
        },
        beforeSend: function() {
            //jQuery("div.overlay").show();
            jQuery("div.loading-image-container").fadeIn();
        },
        error: function() {
            alert("Something went wrong.");
        }
    });
}
// jQuery function to edit Sample cloud video

function edit_sample_video(mediaId) {
    var CurPage = getQueryStringByName('page');
    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        dataType: 'html',
        data: 'action=sp_edit_sample_records&mediaId=' + mediaId + '&curpage=' + CurPage,
        success: function(data) {
            jQuery("div.loading-image-container").fadeOut();
            //jQuery("div.loading-image-container").hide();
            jQuery(".dm-cloud-edit-form").html(data);
            jQuery('#video-tags').tagsInput({
                width: 'auto'
            });
            jQuery("a#editpopup").trigger("click");
        },
        beforeSend: function() {
            jQuery("div.loading-image-container").fadeIn();
            //jQuery("div.loading-image-container").show();
        },
        error: function() {
            alert("Something went wrong.");
        }
    });
}
//Method to display Sample video on fancybox when click on play butoon
jQuery(document).ready(function() {
    jQuery("tr.dm-gallery-rows").click(function(event) {
        if (!jQuery(event.target).is('a')) {
            try {
                var embedUrl = jQuery(this).find('img').attr('alt');
                embedUrl = embedUrl + ajax_object.parameter;
                var duration = jQuery(this).find('span.dm-play-time').text();
                var title = jQuery(this).find('img').attr('title');
                var views = jQuery(this).find('td.Vtitle').find('span.views').text();
                var status = jQuery(this).find('td.Vtitle').find('span.video-status').text();
                var desc = jQuery(this).find('td.Vtitle').find('span.desc').text();
                var tags = jQuery(this).find('td.Vtitle').find('span.tags').text();
                string = '';
                string += '<div class="dm-preview-container dm-common">';
                string += '<iframe width="450" height="250" frameborder="0" scrolling="no" src="' + embedUrl + '"></iframe>';
                string += '<div class="inner">';
                string += '<div class="title">' + title + '</div>';
                string += '<div class="tag-view"><span class="duration">' + secondsTimeSpanToHMS(duration) + '</span> - <span class="one">' + tags + '</span> <span class="two italic">' + views + '</span></div>';
                string += '<div class="publish"><span class="logo"></span> - ' + status + '</div>';
                string += '<div class="break"></div>';
                string += '<div class="desc">' + desc + '</div>';
                string += '<div class="embed-url"><a href="' + embedUrl.replace('embed/', '') + '" target="_blank">' + embedUrl.replace('embed/', '') + '</a></div>';
                string += '</div>';
                string += '</div>';
                jQuery.fancybox(string, {
                    overlayShow: true,
                    hideOnContentClick: false
                });
            } catch (err) {
                txt = "There was an error on this page.\n\n";
                txt += "Error description: " + err.message + "\n\n";
                txt += "Click OK to continue.\n\n";
                alert(txt);
            }
        }
    });
});
//Method to display Sample cloud video on fancybox when click on play butoon
jQuery(document).ready(function() {
    jQuery("tr.dmc-gallery-rows").click(function(event) {
        if (!jQuery(event.target).is('a')) {
            try {
                var embedUrl = jQuery(this).find('img').attr('alt');
                var duration = jQuery(this).find('span.dmc-play-time').text();
                var title = jQuery(this).find('img').attr('title');
                var views = jQuery(this).find('td.Vtitle').find('span').text();
                string = '';
                string += '<div class="dm-preview-container dm-common">';
                string += '<iframe width="450" height="250" frameborder="0" scrolling="no" src="' + embedUrl + '"></iframe>';
                string += '<div class="inner">';
                string += '<div class="title">' + title + '</div>';
                string += '<div class="views"><span class="duration">' + secondsTimeSpanToHMS(duration) + '</span> - <span class="italic">' + views + '</span></div>';
                string += '<div class="logo"></div>';
                string += '</div>';
                string += '</div>';
                jQuery.fancybox(string, {
                    overlayShow: true,
                    hideOnContentClick: false
                });
            } catch (err) {
                txt = "There was an error on this page.\n\n";
                txt += "Error description: " + err.message + "\n\n";
                txt += "Click OK to continue.\n\n";
                alert(txt);
            }
        }
    });
    //Logic to validate metatags values for Sample cloud gallery videos on edit page
    jQuery("a#dmc-new-tag").live('click', function() {
        var error = '';
        var metaValue = jQuery(this).prev('input').val();
        var keyValue = jQuery(this).prev().prev('input').val();
        jQuery(".alert-msg").hide();
        jQuery(this).prev('input').removeClass('missing-value');
        jQuery(this).prev().prev('input').removeClass('missing-value');
        if (metaValue != '' || keyValue != '') {
            if (keyValue != '') {
                if (isNumeric(keyValue)) {
                    error = 'NUMERIC';
                } else if (!(metaValue)) {
                    error = 'CORRES_VALUE_NULL';
                }
            } else {
                error = 'KEY_EMPTY';
            }
            if (error == 'NUMERIC') {
                jQuery("#dmc-message").html("Meta key should not be numeric.").show();
                jQuery(this).prev().prev('input').addClass('missing-value');
                return false;
            } else if (error == 'CORRES_VALUE_NULL') {
                jQuery("#dmc-message").html("Value against to entered meta key should not be empty.").show();
                jQuery(this).prev('input').addClass('missing-value');
                return false;
            } else if (error == 'KEY_EMPTY') {
                jQuery("#dmc-message").html("Meta key value should not be empty.").show();
                jQuery(this).prev().prev('input').addClass('missing-value');
                return false;
            }
            var tagcounter = 2;
            var counter = jQuery('#counter-value').val();
            if (counter) {
                tagcounter = counter;
            }
            tagcounter++;
            jQuery('#counter-value').val(tagcounter);
            if (tagcounter > 5) {
                jQuery("#dmc-message").html("You can not add more tags.").show();
                return false;
            }
            var html = '';
            html += '<div class="tag" id="meta_' + keyValue + '">';
            html += '<label>' + keyValue + '</label>';
            html += '<input type="hidden" value="' + keyValue + '" name="meta[val' + tagcounter + '][]" class="keyInput" size="50">';
            html += '<input type="text" value="' + metaValue + '" name="meta[val' + tagcounter + '][]">';
            html += '<a href="javascript:void(0);" onclick="deleteMetatags(\'\', \'' + keyValue + '\');" class="delete-tag">Remove</a>';
            html += '</div>';
            jQuery("div.present-tags").append(html);
            jQuery(this).prev('input').val('');
            jQuery(this).prev().prev('input').val('');
        } else {
            jQuery("#dmc-message").html("Some information is missing. We've heighlighted the fields for you.").show();
            if (metaValue == '' && keyValue == '') {
                jQuery(this).prev('input').addClass('missing-value');
                jQuery(this).prev().prev('input').addClass('missing-value');
            } else if (keyValue == '') {
                jQuery(this).prev().prev('input').addClass('missing-value');
            } else if (metaValue == '') {
                jQuery(this).prev('input').addClass('missing-value');
            }
        }
    });
});

function secondsTimeSpanToHMS(s) {
    var h = Math.floor(s / 3600); //Get whole hours
    s -= h * 3600;
    var m = Math.floor(s / 60); //Get remaining minutes
    s -= m * 60;
    if (h > 00) {
        return h + ":" + (m < 10 ? '0' + m : m) + ":" + (s < 10 ? '0' + s : s); //zero padding on minutes and seconds
    } else {
        return (m < 10 ? '0' + m : m) + ":" + (s < 10 ? '0' + s : s); //zero padding on minutes and seconds
    }
}
//Jquery method to delete meta tags:

function deleteMetatags(mediaId, key) {
    var data = {
        action: 'sp_delete_dm_cloud_metatags',
        mediaId: mediaId,
        key: key
    };
    jQuery.post(ajax_object.ajax_url, data, function(response) {
        //alert('Got this from the server: ' + response);
        jQuery("#meta_" + key).remove();
    });
}

function getDMCsp_updatedvalues() {
    //Validating key values
    var error = '';
    if (jQuery("#video-title").val() == '') {
        jQuery("#dmc-message").html("Video title should not be empty.").show();
        jQuery("#video-title").addClass("missing-value");
        return false;
    }
    jQuery(".keyInput").each(function() {
        var value = jQuery(this).val();
        if (value != '' || jQuery(this).next('input').val() != '') {
            if (value != '') {
                if (isNumeric(value)) {
                    error = 'NUMERIC';
                } else if (!(jQuery(this).next('input').val())) {
                    error = 'CORRES_VALUE_NULL';
                }
            } else {
                error = 'KEY_EMPTY';
            }
        }
    });
    if (error == 'NUMERIC') {
        jQuery("#dmc-message").html("Meta key should not be numeric.").show();
        return false;
    } else if (error == 'CORRES_VALUE_NULL') {
        jQuery("#dmc-message").html("Value against to entered meta key should not be empty.").show();
        return false;
    } else if (error == 'KEY_EMPTY') {
        jQuery("#dmc-message").html("Meta key value should not be empty.").show();
        return false;
    }
    var tags = '';
    var counterdata = jQuery('#dm_sp_update_form').serialize();
    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: 'action=sp_update_dm_cloud_metatags&' + counterdata,
        success: function(data) {
            if (data) {
                jQuery("#dmc-message").addClass('success-msg');
                jQuery("#dmc-message").html("Video meta sp_update successfully.").show();
                setTimeout("location.reload(true);", 2000);
            }
        },
        error: function() {
            jQuery("#dmc-message").html("Something went wrong.").show();
        }
    });
}

function getSamplesp_updatedvalues() {
    jQuery("div.title-wrap").removeClass("missing-value");
    jQuery("div.channel-wrap").removeClass("missing-value");
    if (jQuery("#video-title").val() == '') {
        jQuery("div.title-wrap").addClass("missing-value");
        jQuery("#dm-message").html("Some information is missing. We've highlighted the fields above for you.").show();
        return false;
    }
    if (jQuery("#channel").val() == '') {
        jQuery("div.channel-wrap").addClass("missing-value");
        jQuery("#dm-message").html("Some information is missing. We've highlighted the fields above for you.").show();
        return false;
    }
    var counterdata = jQuery('#dm_sp_update_form').serialize();
    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: 'action=sp_update_sample_datas&' + counterdata,
        success: function(data) {
            if (data) {
                jQuery("#dm-message").addClass('success-msg');
                jQuery("#dm-message").html("Video data sp_update successfully.").show();
                setTimeout("location.reload(true);", 2000);
            }
        },
        error: function() {
            jQuery("#dm-message").html("Something went wrong.").show();
        }
    });
}
//Callback function to check numeric value

function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

function getQueryStringByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}