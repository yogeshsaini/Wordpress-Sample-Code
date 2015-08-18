jQuery(function ($) {

    var myUploader = new plupload.Uploader({
        browse_button: 'browse_file', // id of the browser button
        multipart: true, // <- this is important because you want              //    to pass other data as well
        multi_selection: false,
        filters : [
            {title : "Image files", extensions : "jpg,gif,png"}
        ],
        url: ajax_object_another.ajaxurl
    });

    myUploader.init();

    myUploader.bind('FilesAdded', function (up, files) {
         myUploader.start();
        // do a console.log(files) to see what file was selected...
    });

    myUploader.bind('Error', function(error){
        var err = "Invalid File format.\n";
        err += "Please upload only .jpg, .Png, .gif";
        alert(err);
     });
    // before upload starts, get the value of the other fields
    // and send them with the file
    myUploader.bind('BeforeUpload', function (up) {
        myUploader.settings.multipart_params = {
            attachment_id : $('#attach_id').val(),
            action: 'action_upload_pattern'
            // add your other fields here...    
        };
    });

    // equivalent of the your "success" callback
    myUploader.bind('FileUploaded', function (up, file, ret) {
        var result = $.parseJSON(ret.response);
        if (result.id) {
            $('#attach_id').val(result.id);
        }

        if (result.url) {
            $('img.edit-video-thumbnail').attr('src', result.url);
            $('#attach_url').val(result.url);
        }
        // $('#browse_file').hide();
        // $('#submitPattern').hide();
    });

    // trigger submission when this button is clicked
    $('#submitPattern').on('click', function (e) {
        myUploader.start();
        e.preventDefault();
    });

});