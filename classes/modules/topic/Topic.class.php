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
            $sText = trim(strip_tags($sText));
        } else {
            $sText = '';
        }
        return $sText;
    }

}

// EOF