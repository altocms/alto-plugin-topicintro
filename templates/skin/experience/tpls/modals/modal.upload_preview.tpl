<div class="modal fade in" id="modal-upload_preview">
    <div class="modal-dialog">
        <div class="modal-content">

            <header class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{$aLang.plugin.topicintro.modal_introimage_title}</h4>
            </header>

            <div class="modal-body">
                <form id="preview-upload-form" method="POST" enctype="multipart/form-data" onsubmit="return false;">

                    <div class="form-group topic-photo-upload-input">
                        <div class="input-group">
                            <span class="input-group-addon">{$aLang.uploadimg_file}</span>
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                <div class="form-control" data-trigger="fileinput">
                                    <i class="fa fa-file fileinput-exists"></i>
                                    <span class="fileinput-filename"></span>
                                </div>
                                        <span class="input-group-addon btn btn-default btn-file" >
                                            <span style="cursor: pointer"  class="fileinput-new">{$aLang.select}</span>
                                            <span style="cursor: pointer"  class="fileinput-exists">{$aLang.select}</span>
                                            <input type="file" id="preview-upload-file" name="intro_image"/>
                                        </span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn"  data-dismiss="modal" aria-hidden="true">
                        {$aLang.text_cancel}
                    </button>
                    <button type="submit" class="btn btn-primary"  onclick="ls.introImage.upload();">
                        {$aLang.plugin.topicintro.modal_introimage_upload_choose}
                    </button>

                    <input type="hidden" name="is_iframe" value="true"/>
                    <input type="hidden" name="topic_id" value="{$_aRequest.topic_id}"/>
                </form>
            </div>
        </div>
    </div>
</div>
