{if $oTopic->getPreviewImageUrl() AND !$oTopic->getFirstImage(true)}
    <img src="{$oTopic->getPreviewImageUrl()}" class="topic-content-preview-img">
{/if}
