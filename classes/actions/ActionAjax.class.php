<?php
/*---------------------------------------------------------------------------
 * @Project: Alto CMS
 * @Project URI: http://altocms.com
 * @Description: Advanced Community Engine
 * @Copyright: Alto CMS Team
 * @License: GNU GPL v2 & MIT
 *----------------------------------------------------------------------------
 */

class PluginTopicintro_ActionAjax extends PluginTopicintro_Inherits_ActionAjax {

    /**
     * Registers events
     */
    protected function RegisterEvent() {

        parent::RegisterEvent();

        if ($this->User_IsAuthorization()) {
            $this->AddEvent('introimage', 'EventIntroimage');
        }
    }

    /**
     * @return bool|string
     */
    public function EventIntroimage() {

        $aUploadedFile = $this->GetUploadedFile();
        if (!$aUploadedFile) {
            $this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            F::SysWarning('System Error');
            return false;
        }

        $sImageUrl = $this->Topic_UploadTopicIntroimage($aUploadedFile);
        if (!$sImageUrl) {
            $this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            F::SysWarning('System Error');
            return false;
        } else {
            $iTopicId = intval(F::GetRequestStr('topic_id'));
            if ($iTopicId) {
                // If topic exists then sets preview image for one
                $oTopic = $this->Topic_GetTopicById($iTopicId);
                if (!$oTopic) {
                    $this->Message_AddError('Topic not found by ID ' . $iTopicId, 'Error');
                    F::SysWarning('System Error');
                    return false;
                }
                $oTopic->setPreviewImage($sImageUrl);
                $this->Topic_UpdateTopic($oTopic);
            } else {
                // ... else saves image url in session
                $this->Topic_SetTmpIntroimage($sImageUrl);
            }
            // generates preview image
            $xSize = F::GetRequestStr('image_size');
            if (!$xSize) {
                $xSize = Config::Val('plugin.topicintro.preview.size.edit', 'x100');
            }
            $sModSuffix = F::File_ImgModSuffix($xSize, strtolower(pathinfo($sImageUrl, PATHINFO_EXTENSION)));
            $sImageUrl = $sImageUrl . $sModSuffix;
            if (Config::Get('module.image.autoresize')) {
                $sFile = $this->Uploader_Url2Dir($sImageUrl);
                if (!F::File_Exists($sFile)) {
                    $this->Img_Duplicate($sFile);
                }
            }
            $this->Viewer_AssignAjax('image', $sImageUrl);
            $this->Message_AddNotice($this->Lang_Get('plugin.topicintro.topic_create_preview_added'), $this->Lang_Get('attention'));
        }
        return true;
    }

}

// EOF