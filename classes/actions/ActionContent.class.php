<?php
/*---------------------------------------------------------------------------
 * @Project: Alto CMS
 * @Project URI: http://altocms.com
 * @Description: Advanced Community Engine
 * @Copyright: Alto CMS Team
 * @License: GNU GPL v2 & MIT
 *----------------------------------------------------------------------------
 */

class PluginTopicintro_ActionContent extends PluginTopicintro_Inherits_ActionContent {

    protected function checkTopicFields($oTopic) {

        $sIntroText = $this->Topic_ParseIntroText(F::GetRequestStr('topic_intro_text'));
        $oTopic->setIntroText($sIntroText);

        $nLen = mb_strlen($sIntroText, 'UTF-8');
        $nMax = intval(Config::Get('plugin.topicintro.introtext.max_size'));
        $bResult = true;

        if ($nMax && ($nLen > $nMax)) {
            $this->Message_AddError(
                $this->Lang_Get('plugin.topicintro.topic_create_intro_text_error', array('len' => $nLen, 'max'=>$nMax)),
                $this->Lang_Get('error')
            );
            $bResult = false;
        }

        if ($oTopic->getAutopreview() && Config::Get('plugin.topicintro.autopreview.enable')) {
            // set preview image to null because it will be created automatically
            $oTopic->setPreviewImage(null);
        }
        return $bResult && parent::checkTopicFields($oTopic);
    }

    protected function EventEdit() {

        $this->Hook_AddExecFunction('topic_edit_show', array($this, '_topicEditShow'));
        return parent::EventEdit();
    }

    public function _topicEditShow($aParams) {

        if (!isset($_REQUEST['topic_intro_text']) && isset($aParams['oTopic']) && ($oTopic = $aParams['oTopic'])) {
            $_REQUEST['topic_intro_text'] = $oTopic->getIntroText();
        }
    }
}

// EOF