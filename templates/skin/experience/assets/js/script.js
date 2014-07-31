/*---------------------------------------------------------------------------
 * @Project: Alto CMS
 * @Project URI: http://altocms.com
 * @Description: Advanced Community Engine
 * @Copyright: Alto CMS Team
 * @License: GNU GPL v2 & MIT
 *----------------------------------------------------------------------------
 */

var ls = ls || { };

ls.introImage = ( function ($) {
    var $that = this;

    this.options = {
        modal: '#modal-upload_preview',
        image: null
    };

    this.showModal = function(button, image) {
        $($that.options.modal).modal();
        $that.options.image = $(image);
    };

    this.hideModal = function() {
        $($that.options.modal).modal('hide');
    };

    this.upload = function () {
        var input = $('#preview-upload-file'),
            topicId = input.data('topic-id'),
            form = $('<form method="post" enctype="multipart/form-data">' +
                '<input type="hidden" name="is_iframe" value="true" />' +
                '<input type="hidden" name="ALTO_AJAX" value="1" />' +
                '<input type="hidden" name="topic_id" value="' + topicId + '" />' +
                '</form>').hide().appendTo('body');

        if (!topicId) {
            topicId = input.parents('form').first().find('input[name=topic_id]').val();
            if (topicId) {
                form.find('input[name=topic_id]').val(topicId);
            }
        }

        input.clone(true).insertAfter(input);
        input.appendTo(form);

        ls.progressStart();
        ls.ajaxSubmit(ls.routerUrl('ajax') + 'introimage/', form, function (response) {
            ls.progressDone();
            if (!response) {
                ls.msg.error(null, 'System error #1001');
            } else if (response.bStateError) {
                ls.msg.error(response.sMsgTitle, response.sMsg);
            } else {
                ls.introImage.addImage(response.image);
                ls.introImage.hideModal();
                if (response.sMsg) {
                    ls.msg.notice(response.sMsgTitle ? response.sMsgTitle : '', response.sMsg);
                }
            }
            form.remove();
        });
    };

    this.addImage = function(imageUrl) {
        if (imageUrl) {
            $($that.options.image).prop('src', imageUrl).show();
        } else {
            $($that.options.image).prop('src', '').hide();
        }
    };

    return this;
}).call(ls.introImage || { }, jQuery);

// EOF