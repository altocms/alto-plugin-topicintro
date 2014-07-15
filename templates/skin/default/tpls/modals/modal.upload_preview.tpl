<div class="modal fade in" id="modal-upload_preview">
    <div class="modal-dialog">
        <div class="modal-content">

            <header class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{$aLang.plugin.topicintro.modal_introimage_title}</h4>
            </header>

            <div class="modal-body">
                <form id="preview-upload-form" method="POST" enctype="multipart/form-data" onsubmit="return false;">

                    <div id="topic-photo-upload-input" class="topic-photo-upload-input">
                        <label for="preview-upload-file">{$aLang.plugin.topicintro.modal_introimage_choose_image}:</label><br/>
                        <div class="btn btn-default btn-file">
                            {$aLang.uploadimg_file}
                            <input type="file" id="preview-upload-file" name="intro_image"/>
                        </div>
                        <br><br>

                        <button type="submit" class="btn"  data-dismiss="modal" aria-hidden="true">
                            {$aLang.text_cancel}
                        </button>
                        <button type="submit" class="btn btn-primary"  onclick="ls.introImage.upload();">
                            {$aLang.plugin.topicintro.modal_introimage_upload_choose}
                        </button>

                        <input type="hidden" name="is_iframe" value="true"/>
                        <input type="hidden" name="topic_id" value="{$_aRequest.topic_id}"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
