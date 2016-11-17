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

    /**
     * @param $oTopic
     *
     * @return bool
     */
    protected function checkTopicFields($oTopic) {

        $bResult = true;
        if (Config::Get('plugin.topicintro.introtext.enable')) {
            $sIntroText = $this->Topic_ParseIntroText(F::GetRequestStr('topic_intro_text'));
            // introtext will be saved in topic
            $oTopic->setIntroText($sIntroText);

            // defines length of introtext without tags
            $nLen = mb_strlen(strip_tags($sIntroText), 'UTF-8');
            $nMax = (int)Config::Get('plugin.topicintro.introtext.max_size');

            if ($nMax && ($nLen > $nMax)) {
                $this->Message_AddError(
                    $this->Lang_Get('plugin.topicintro.topic_create_intro_text_error', array('len' => $nLen, 'max'=>$nMax)),
                    $this->Lang_Get('error')
                );
                $bResult = false;
            }
        }

        if ($oTopic->getAutopreview() && Config::Get('plugin.topicintro.autopreview.enable')) {
            // set preview image to null because it will be created automatically
            $oTopic->setPreviewImage(null);
        }
        if (!$oTopic->getAutopreview() && !Config::Get('plugin.topicintro.preview.edit')) {
            // set preview image to null because it will be created automatically
            $oTopic->setPreviewImage(null);
        }
        return $bResult && parent::checkTopicFields($oTopic);
    }

    /**
     * @return mixed
     */
    protected function EventEdit() {

        $this->Hook_AddExecFunction('topic_edit_show', array($this, '_topicEditShow'));
        return parent::EventEdit();
    }

    /**
     * @param $aParams
     */
    public function _topicEditShow($aParams) {

        if (!isset($_REQUEST['topic_intro_text']) && isset($aParams['oTopic']) && ($oTopic = $aParams['oTopic'])) {
            $_REQUEST['topic_intro_text'] = $oTopic->getIntroText('');
        }
    }

    /**
     * Adds new topic
     *
     * @param $oTopic
     *
     * @return mixed
     */
    protected function _addTopic($oTopic) {

        $sImageUrl = $this->Topic_GetTmpIntroimage();
        if ($sImageUrl) {
            $oTopic->setPreviewImage($sImageUrl);
            $this->Topic_DelTmpIntroimage();
        }
        return parent::_addTopic($oTopic);
    }

}

// EOF