jQuery(document).ready(function($){
    
    var mediaUploader;
    var mediaUploader2;
    
    $('#upload_aspl_pb_new_lable_image').click(function(e) {

        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            }, multiple: false 
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#aspl_pb_new_lable_image').val(attachment.url);
        });

        mediaUploader.open();

    });
     
    $('#upload_aspl_pb_soldout_lable_image').click(function(e) {

        e.preventDefault();
        if (mediaUploader2) {
            mediaUploader2.open();
            return;
        }

        mediaUploader2 = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            }, multiple: false 
        });

        mediaUploader2.on('select', function() {
            var attachment = mediaUploader2.state().get('selection').first().toJSON();
            $('#aspl_pb_soldout_lable_image').val(attachment.url);
        });

        mediaUploader2.open();

    });


});