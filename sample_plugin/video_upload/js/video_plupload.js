jQuery.fn.exists = function () {
    return jQuery(this).length > 0;
}
jQuery(document).ready(function ($) {
    if ($(".plupload-upload-uic").exists()) {
        var pconfig = false;
        $(".plupload-upload-uic").each(function () {
            var $this = $(this);
            var erro_file_ids = new Array();
            var id1 = $this.attr("id");
            var video_id = id1.replace("plupload-upload-ui", "");
            plu_show_thumbs(video_id);
            pconfig = JSON.parse(JSON.stringify(base_plupload_config));
            pconfig["browse_button"] = video_id + pconfig["browse_button"];
            pconfig["container"] = video_id + pconfig["container"];
            pconfig["drop_element"] = video_id + pconfig["drop_element"];
            pconfig["file_data_name"] = video_id + pconfig["file_data_name"];
            pconfig["multipart_params"]["video_id"] = video_id;
            pconfig["multipart_params"]["_ajax_nonce"] = $this.find(".ajaxnonceplu").attr("id").replace("ajaxnonceplu", "");
            if ($this.hasClass("plupload-upload-uic-multiple")) {
                pconfig["multi_selection"] = true;
            }
            if ($this.find(".plupload-resize").exists()) {
                var w = parseInt($this.find(".plupload-width").attr("id").replace("plupload-width", ""));
                var h = parseInt($this.find(".plupload-height").attr("id").replace("plupload-height", ""));
                pconfig["resize"] = {
                    width: w,
                    height: h,
                    quality: 90
                };
            }
            var uploader = new plupload.Uploader(pconfig);
            uploader.bind('Init', function (up) {});
            uploader.init();
            
            // a file was added in the queue
            uploader.bind('FilesAdded', function (up, files) {                
                    $('#full_wrapper .action_wrappers').fadeIn('slow');
                    $.each(files, function (i, file) {
                        if ($.inArray(file.id, erro_file_ids) == -1) {
                             $("div#full_wrapper").find('.filelist').append('<div class="file" id="' + file.id + '"><div class="file_name">' + file.name + '</div><div class="file_progress_wrapper"><div class="seco_progress_wrapper"><div class="fileprogress"></div></div><span class="pro_text"></span></div><div class="file_size">' + plupload.formatSize(file.size) + '</div><div class="file_action"><a href="#" class="remove_file_action" data-file="' + i + '">Remove</a></div></div>');
                        }
                    });
                    up.refresh();
            });
            
            uploader.bind('UploadProgress', function (up, file) {
                $('#' + file.id + " .file_progress_wrapper").addClass('up_prog_load');
                $('#' + file.id + " .fileprogress").width(file.percent + "%");
                if (parseInt(file.percent) == 100) {
                   $('#' + file.id + " .pro_text").html('<img src="'+ video_plupload_object.plugin_url +'/assets/img/encoding.gif" alt="" />'); 
                } else {
                   $('#' + file.id + " .pro_text").html(file.percent + "%");
                }
                //$('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
            });
            
            // a file was uploaded
            uploader.bind('FileUploaded', function (up, file, response) {
                if (this.total.queued == 0) {
                    uploader.refresh();
                    uploader.trigger("DisableBrowse", false);
                    $('#full_wrapper .action_wrappers').fadeOut('slow');
                    $('#full_wrapper .upload_message').fadeOut('slow');
                    $('#full_wrapper .filelist').removeClass('filelist_progress');
                }
                responsemessage = (response["response"]).split("|+|");
                if (responsemessage[0] == 'error') {
                    $('#' + file.id + " .file_progress_wrapper").addClass('error_upload');
                    $('#' + file.id + " .fileprogress").html(responsemessage[2]);
                    $('#full_wrapper .upload_message').addClass('upload_error_message').html('<span class="bold">'+responsemessage[1]+'&nbsp;</span>' + responsemessage[2]).show();
                } else {
                    $('#' + file.id).fadeOut();
                    response = response["response"]
                    // add url to the hidden field
                    if ($this.hasClass("plupload-upload-uic-multiple")) {
                        // multiple
                        var v1 = $.trim($("#" + video_id).val());
                        if (v1) {
                            v1 = v1 + "," + response;
                        } else {
                            v1 = response;
                        }
                        $("#" + video_id).val(v1);
                    } else {
                        // single
                        $("#" + video_id).val(response + "");
                    }
                    // show thumbs
                    plu_show_thumbs(video_id);
                }
            });
            
            uploader.bind('FilesRemoved', function (up, file) {
                //console.log(file);
                //console.log(up);
            });
            
            uploader.bind('Error', function (up, args) {
                erro_file_ids.push(args.file.id);
                alert(args.message);
                //$('#full_wrapper .upload_message').addClass('upload_error_message').html(args.message).show();
                up.refresh();
            });
            
            $('#submitPattern').on('click', function (e) {
                pconfig["multipart_params"]["account_name"] = $('#account_name').val();
                $('#full_wrapper .upload_message').removeClass('upload_error_message').html('<span class="up_image"></span><span class="bold">Uploading:&nbsp;</span>Please don\'t refresh the page or close the browser tab.').fadeIn();
                $('#full_wrapper .filelist').addClass('filelist_progress');
                uploader.start();
                uploader.trigger("DisableBrowse", true);
                e.preventDefault();
            });
            
            $('#full_wrapper').on("click", "a.remove_file_action", function (e) {
                uploader.splice(jQuery(this).attr('data-file'), 1);
                uploader.refresh();
                $(this).closest("div.file").fadeOut('slow', function () {
                    $(this).closest("div.file").remove();
                    var count = $("#full_wrapper .filelist").children().length;
                    if (count == 0) {
                        $('#full_wrapper .action_wrappers').hide();
                    }
                });
                e.preventDefault();
            });
        });
    }
});

function plu_show_thumbs(video_id) {
    var $ = jQuery;
    var thumbsC = $("#" + video_id + "plupload-thumbs");
    thumbsC.html("");
    var imagesS = $("#" + video_id).val();
    var images = imagesS.split(",");
    for (var i = 0; i < images.length; i++) {
        if (images[i]) {
            var imagedat = (images[i]).split("|+|");
            var thumb = $('<div class="file" id="' + imagedat[1] + '"><div class="file_name">' + imagedat[0] + '</div><div class="file_progress_wrapper prog_comp"><div class="seco_progress_wrapper"><div class="fileprogress">Uploaded</div></div><span class="pro_text">100%</span></div><div class="file_size">' + imagedat[3] + '</div><div class="file_action"><a href="#" id="' +imagedat[1]+'" class="edit_file_action" rel="' + imagedat[4] + '">Edit</a></div></div>');
            thumbsC.append(thumb);
            thumb.find("a").click(function () {
                var ki = $(this).attr("rel");
                var mediaid = $(this).attr("id");
                if(ki == 'cloud') {
                    editDMCvideo(mediaid);
                } else if (ki == 'sample') {
                    edit_sample_video(mediaid);
                }
                return false;
            });
        }
    }
}