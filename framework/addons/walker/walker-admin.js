(function ($) {
    'use strict';
    // Uploading files
    var file_frame;
    var clickedID;

    $(document).off('click', '.btn_upload_img').on('click', '.btn_upload_img', function (event) {

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if (!file_frame) {
            // Create the media frame.
            file_frame = wp.media.frames.downloadable_file = wp.media({
                title: 'Choose an image',
                button: {
                    text: 'Use image'
                },
                multiple: false
            });
        }

        file_frame.open();

        clickedID = $(this).attr('id');

        // When an image is selected, run a callback.
        file_frame.on('select', function () {
            var attachment = file_frame.state().get('selection').first().toJSON();

            $('#' + clickedID).val(attachment.url);
            if ($('#' + clickedID).attr('data-name'))
                $('#' + clickedID).attr('name', $('#' + clickedID).attr('data-name'));

            file_frame.close();
        });
    });

    $(document).on('click', '.btn_remove_img', function (event) {

        var clickedID = $(this).attr('id');
        $('#' + clickedID).val('');
        $('#' + clickedID + '_thumb').html('');

        return false;
    });
})(jQuery);