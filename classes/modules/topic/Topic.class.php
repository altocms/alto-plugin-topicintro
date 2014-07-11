<?php
/*---------------------------------------------------------------------------
 * @Project: Alto CMS
 * @Project URI: http://altocms.com
 * @Description: Advanced Community Engine
 * @Copyright: Alto CMS Team
 * @License: GNU GPL v2 & MIT
 *----------------------------------------------------------------------------
 */

class PluginTopicintro_ModuleTopic extends PluginTopicintro_Inherits_ModuleTopic {

    public function ParseIntroText($sText) {

        if ($sText && is_scalar($sText)) {
            if (!Config::Get('plugin.topicintro.introtext.html_tags')) {
                $sText = strip_tags($sText);
            }
            $sText = trim($sText);
        } else {
            $sText = '';
        }
        return $sText;
    }

}

// EOF