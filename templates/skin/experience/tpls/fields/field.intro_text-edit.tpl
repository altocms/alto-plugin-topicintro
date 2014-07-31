<script>
    jQuery(document).ready(function ($) {
        var groupText = $('form .form-group').has('textarea[name=topic_text]');
        var introText = $('.form-group.js-topic_intro_text');
        var maxLength = parseInt('{Config::Get('plugin.topicintro.introtext.max_size')}');

        if (groupText.length && introText.length) {
            introText.insertBefore(groupText).show();
            if (maxLength > 0) {
                //introText.find('textarea').attr('maxlength', maxLength);
            }
        }
    });
</script>

<div class="form-group js-topic_intro_text" style="display: none;">
    <label for="topic_intro_text">{$aLang.plugin.topicintro.topic_create_intro_text}</label>
    <textarea name="topic_intro_text" id="topic_intro_text" rows="4"
              class="form-control
              {if Config::Get('plugin.topicintro.introtext.html_tags')}js-editor-wysiwyg js-editor-markitup{/if}"
            >{$_aRequest.topic_intro_text}</textarea>

    <p class="help-block">
        <small>{$aLang.plugin.topicintro.topic_create_intro_text_note}</small>
    </p>
</div>
