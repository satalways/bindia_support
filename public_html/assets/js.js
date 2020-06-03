var $ = jQuery;
$(function () {
    $('textarea.summernote').summernote({
        height: '400'
    });
    $('.ezdz').ezdz({
        text: 'Drop one or more files'
    });
    $(document).on('click', '.reply_link', function (e) {
        e.preventDefault();
        var obj = $('#ticket_reply_form textarea[name=content]');
        obj.summernote('focus');
        $('#reply_title')[0].scrollIntoView({ behavior: 'smooth' });
    });
});
