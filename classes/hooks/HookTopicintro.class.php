<?php
/*---------------------------------------------------------------------------
 * @Project: Alto CMS
 * @Project URI: http://altocms.com
 * @Description: Advanced Community Engine
 * @Copyright: Alto CMS Team
 * @License: GNU GPL v2 & MIT
 *----------------------------------------------------------------------------
 */

class PluginTopicintro_HookTopicintro extends Hook {

    public function RegisterHook() {

        if (Config::Get('plugin.topicintro.introtext.enable') || Config::Get('plugin.topicintro.preview.edit')) {
            $this->AddHook('template_add_topic_begin', 'TplAddTopicBegin');
        }

        if (Config::Get('plugin.topicintro.introtext.enable')) {
            if ($sHook = Config::Get('plugin.topicintro.introtext.hook_list')) {
                $this->AddHook($sHook, 'InjectIntroTextInList');
            }

            if ($sHook = Config::Get('plugin.topicintro.introtext.hook_show')) {
                $this->AddHook($sHook, 'InjectIntroTextInShow');
            }
        }

        if ($sHook = Config::Get('plugin.topicintro.autopreview.hook_list')) {
            $this->AddHook($sHook, 'InjectPreviewInList');
        }

        if ($sHook = Config::Get('plugin.topicintro.autopreview.hook_show')) {
            $this->AddHook($sHook, 'InjectPreviewInShow');
        }

    }

    public function TplAddTopicBegin() {

        $sResult = '';
        if (Config::Get('plugin.topicintro.introtext.enable')) {
            $sTemplate = Plugin::GetTemplateDir(__CLASS__) . 'tpls/fields/field.intro_text-edit.tpl';
            $sResult = $this->Viewer_Fetch($sTemplate);
        }

        if (Config::Get('plugin.topicintro.preview.edit')) {
            $sTemplate = Plugin::GetTemplateDir(__CLASS__) . 'tpls/fields/field.preview_img-edit.tpl';
            $sResult .= $this->Viewer_Fetch($sTemplate);
        }

        return $sResult;
    }

    /**
     * @param $aParams
     *
     * @return string|null
     */
    public function InjectIntroTextInList($aParams) {

        if (isset($aParams['bTopicList']) && $aParams['bTopicList'] && (isset($aParams['topic'])) || isset($aParams['oTopic'])) {
            $oTopic = (isset($aParams['topic']) ? $aParams['topic'] : $aParams['oTopic']);
            $this->Viewer_Assign('oTopic', $oTopic);
            return $this->Viewer_Fetch(Plugin::GetTemplateDir(__CLASS__) . 'tpls/fields/field.intro_text-list.tpl');
        }
        return null;
    }

    /**
     * @param $aParams
     *
     * @return string|null
     */
    public function InjectIntroTextInShow($aParams) {

        if (isset($aParams['bTopicList']) && !$aParams['bTopicList'] && (isset($aParams['topic'])) || isset($aParams['oTopic'])) {
            $oTopic = (isset($aParams['topic']) ? $aParams['topic'] : $aParams['oTopic']);
            $this->Viewer_Assign('oTopic', $oTopic);
            return $this->Viewer_Fetch(Plugin::GetTemplateDir(__CLASS__) . 'tpls/fields/field.intro_text-show.tpl');
        }
        return null;
    }

    /**
     * @param $aParams
     *
     * @return string|null
     */
    public function InjectPreviewInList($aParams) {

        if (isset($aParams['bTopicList']) && $aParams['bTopicList'] && (isset($aParams['topic'])) || isset($aParams['oTopic'])) {
            $oTopic = (isset($aParams['topic']) ? $aParams['topic'] : $aParams['oTopic']);
            $this->Viewer_Assign('oTopic', $oTopic);
            return $this->Viewer_Fetch(Plugin::GetTemplateDir(__CLASS__) . 'tpls/fields/field.preview_img-list.tpl');
        }
        return null;
    }

    /**
     * @param $aParams
     *
     * @return string|null
     */
    public function InjectPreviewInShow($aParams) {

        if (isset($aParams['bTopicList']) && !$aParams['bTopicList'] && (isset($aParams['topic'])) || isset($aParams['oTopic'])) {
            $oTopic = (isset($aParams['topic']) ? $aParams['topic'] : $aParams['oTopic']);
            $this->Viewer_Assign('oTopic', $oTopic);
            return $this->Viewer_Fetch(Plugin::GetTemplateDir(__CLASS__) . 'tpls/fields/field.preview_img-show.tpl');
        }
        return null;
    }

}

// EOF