{include_once file='modals/modal.upload_preview.tpl'}

<script>
    jQuery(document).ready(function ($) {
        var textField = $('form .form-group').has('textarea[name=topic_text]');
        var introImageField = $('.form-group.js-topic_preview');

        if (textField.length && introImageField.length) {
            introImageField.insertBefore(textField).show();
        }

        $('.js-topic_preview_btn').click(function(){
            ls.introImage.showModal(this, '.js-topic_preview_img');
            return false;
        });
    });
</script>

{if Config::Get('plugin.topicintro.preview.edit')}
<div class="form-group topc-edit-preview js-topic_preview">
    <label>{$aLang.plugin.topicintro.topic_create_preview_label}</label>
    <div class="clearfix">
        <div class="topc-edit-preview-wrapper">
            {$xSize=Config::Val('plugin.topicintro.preview.size.edit', 'x100')}
            {if $oTopic}
                {$sTopicPreviewImage = $oTopic->getPreviewImageUrl($xSize)}
            {else}
                {$sTopicPreviewImage = E::Topic_GetTmpIntroimage($xSize)}
            {/if}
            {if $sTopicPreviewImage}
                <img src="{$sTopicPreviewImage}" class="topic-content-preview-img js-topic_preview_img">
            {else}
                <img src="" class="topic-content-preview-img js-topic_preview_img" style="display: none;">
            {/if}
        </div>
        <div class="topc-edit-preview-text">
            <div class="topc-edit-preview-note">
                {if Config::Get('plugin.topicintro.autopreview.enable') AND !$sTopicPreviewImage}
                    {$aLang.plugin.topicintro.topic_create_preview_auto}
                {else}
                    {$aLang.plugin.topicintro.topic_create_preview_note}
                {/if}
            </div>
            <button class="btn btn-default js-topic_preview_btn">
                {if $sTopicPreviewImage}
                    {$aLang.plugin.topicintro.topic_create_preview_update}
                {else}
                    {$aLang.plugin.topicintro.topic_create_preview_add}
                {/if}
            </button>
        </div>
    </div>
</div>
{/if}
