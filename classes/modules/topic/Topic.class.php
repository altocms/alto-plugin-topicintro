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

    /**
     * @param $sText
     *
     * @return string
     */
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

    /**
     * @param $aFile
     *
     * @return bool|string
     */
    public function UploadTopicIntroimage($aFile) {

        if ($sFileTmp = $this->Uploader_UploadLocal($aFile)) {
            return $this->_saveTopicImage($sFileTmp, $this->oUserCurrent, 'introimage');
        }
        return false;
    }

    /**
     * Returns session key for tmp intro image
     *
     * @return string
     */
    public function GetTmpIntroimageKey() {

        return 'introimage-tmp-key-' . $this->Security_GetSecurityKey();
    }

    /**
     * Sets intro image to session
     *
     * @param string $sImageUrl
     */
    public function SetTmpIntroimage($sImageUrl) {

        $sKey = $this->GetTmpIntroimageKey();
        $this->Session_Set($sKey, $sImageUrl);
    }

    /**
     * Gets intro image from session
     *
     * @param string $xSize
     *
     * @return string|null
     */
    public function GetTmpIntroimage($xSize = null) {

        $sKey = $this->GetTmpIntroimageKey();
        $sImageUrl = $this->Session_Get($sKey);
        if ($sImageUrl) {
            $sModSuffix = F::File_ImgModSuffix($xSize, strtolower(pathinfo($sImageUrl, PATHINFO_EXTENSION)));
            $sImageUrl = $sImageUrl . $sModSuffix;
            if (Config::Get('module.image.autoresize')) {
                $sFile = $this->Uploader_Url2Dir($sImageUrl);
                if (!F::File_Exists($sFile)) {
                    $this->Img_Duplicate($sFile);
                }
            }
        }
        return $sImageUrl;
    }

    /**
     * Deletes intro image from session
     *
     */
    public function DelTmpIntroimage() {

        $sKey = $this->GetTmpIntroimageKey();
        $this->Session_Drop($sKey);
    }

}

// EOF