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

        if ($sHook = Config::Get('plugin.topicintro.autopreview.hook')) {
            $this->AddHook($sHook, 'InjectPreview');
        }
    }

    /**
     * Добавляем в стандартную админку ссылку на конвертер фото-сетов
     */
    public function InjectPreview($aParams) {

        if (isset($aParams['bTopicList']) && $aParams['bTopicList'] && (isset($aParams['topic'])) || isset($aParams['oTopic'])) {
            $oTopic = (isset($aParams['topic']) ? $aParams['topic'] : $aParams['oTopic']);
            $this->Viewer_Assign('oTopic', $oTopic);
            return $this->Viewer_Fetch(Plugin::GetTemplateDir(__CLASS__) . 'tpl/inject_preview.tpl');
        }
    }

}

// EOF