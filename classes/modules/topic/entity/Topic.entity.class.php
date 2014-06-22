<?php
/*---------------------------------------------------------------------------
 * @Project: Alto CMS
 * @Project URI: http://altocms.com
 * @Description: Advanced Community Engine
 * @Copyright: Alto CMS Team
 * @License: GNU GPL v2 & MIT
 *----------------------------------------------------------------------------
 */

class PluginTopicintro_ModuleTopic_EntityTopic extends PluginTopicintro_Inherits_ModuleTopic_EntityTopic {

    const DEFAULT_PREVIEW_SIZE = 300;

    // LS compatibility
    public function getPreviewImageWebPath($sSize = null) {

        return $this->getPreviewImageUrl($sSize);
    }

    public function setPreviewImage($data) {

        $this->setExtraValue('preview_image', $data);
    }

    public function setAutopreview($data) {

        $this->setExtraValue('preview_image_is_auto', $data ? true : false);
    }

    public function getAutopreview() {

        return $this->getExtraValue('preview_image_is_auto');
    }

    public function setAutoPreviewImage($data) {

        $this->setPreviewImage($data);
        $this->setAutopreview(true);
    }

    /**
     * @return mixed
     */
    protected function _seekProtoImages() {

        $sText = $this->getText();
        $aResult = array();
        if (preg_match('~\<img\s.*src\s*=\s*([^\s]+)\s*\/\>~siU', $sText, $aM, PREG_OFFSET_CAPTURE)) {
            $sImg = trim($aM[1][0]);
            if (substr($sImg, 0, 1) == '"' || substr($sImg, 0, 1) == '\'') {
                $sImg = substr($sImg, 1, strlen($sImg) - 2);
            }
            // $aM[0][1] - position
            $aResult[$aM[0][1]] = $sImg;
        }

        if (Config::Get('plugin.topicintro.autopreview.video')) {
            $aData = $this->PluginTopicintro_ModuleVideoinfo_ParseText($sText);
            if ($aData) {
                foreach($aData as $aVideoInfo) {
                    $aResult[$aVideoInfo['pos']] = $aVideoInfo['info']['thumbnail'];
                }
            }
        }

        return $aResult;
    }
    /**
     * @return string
     */
    public function getFirstImage() {

        $sImg = $this->getProp('_first_image_url');
        if (is_null($sImg)) {
            $sImg = '';
            if ($aImg = $this->_seekProtoImages()) {
                if (sizeof($aImg) > 1) {
                    ksort($aImg);
                }
                $sImg = reset($aImg);
            }
            $this->setProp('_first_image_url', $sImg);
        }
        return $sImg;
    }

    /**
     * @return string
     */
    public function getPreviewImage() {

        $sPreviewImage = $this->getExtraValue('preview_image');
        if (is_null($sPreviewImage)) {
            if ($nId = $this->getPhotosetMainPhotoId()) {
                $oTopicPhoto = $this->Topic_GetTopicPhotoById($nId);
                if ($oTopicPhoto) {
                    $sPreviewImage = $oTopicPhoto->getUrl();
                }
            }
            if (!$sPreviewImage && Config::Get('plugin.topicintro.autopreview.enable')) {
                $sPreviewImage = $this->getFirstImage();
            }
            $this->setPreviewImage($sPreviewImage ? $sPreviewImage : false);
            $this->setAutopreview(true);
            if (Config::Get('plugin.topicintro.autopreview.autosave')) {
                $this->Topic_UpdateTopic($this);
            }
        }
        return $sPreviewImage;
    }


    public function getPreviewImageUrl($xSize = null) {

        if ($sUrl = $this->getPreviewImage()) {
            if (F::File_IsLocalUrl($sUrl)) {
                if (!$xSize) {
                    $xSize = Config::Get('plugin.topicintro.preview_size.default');
                    if (!$xSize) {
                        $xSize = self::DEFAULT_PREVIEW_SIZE;
                    }
                } elseif ($xPresetSize = Config::Get('plugin.topicintro.preview_size.' . $xSize)){
                    $xSize = $xPresetSize;
                }
                if (is_numeric($xSize) && intval($xSize) == $xSize) {
                    if (Config::Get('plugin.topicintro.single_width')) {
                        $xSize = 'x' . $xSize;
                    } else {
                        $xSize = $xSize . 'x' . $xSize;
                    }
                }
                $sModSuffix = F::File_ImgModSuffix($xSize, strtolower(pathinfo($sUrl, PATHINFO_EXTENSION)));
                $sUrl = $sUrl . $sModSuffix;
                if (Config::Get('module.image.autoresize')) {
                    $sFile = $this->Uploader_Url2Dir($sUrl);
                    if (!F::File_Exists($sFile)) {
                        $this->Img_Duplicate($sFile);
                    }
                }
            }
            return $sUrl;
        } else {
            return null;
        }
    }

    /* Intro text */

    /**
     * Set intro text for this topic
     *
     * @param $data
     */
    public function setIntroText($data) {

        $this->setExtraValue('text_intro', $data);
    }

    /**
     * Returns intro text (announce)
     *
     * @param $sPostfix
     *
     * @return mixed
     */
    public function getIntroText($sPostfix = '...') {

        $sIntroText = $this->getExtraValue('text_intro');
        if (!$sIntroText && Config::Get('plugin.topicintro.introtext.autocreate')) {
            $sIntroText = $this->Topic_ParseIntroText($this->getText());
            $nMax = intval(Config::Get('plugin.topicintro.introtext.max_size'));
            $nLen = mb_strlen($sIntroText, 'UTF-8');
            if ($nMax && $nLen > $nMax) {
                $sIntroText = F::TruncateText($sIntroText, $nMax, $sPostfix, true);
            }
        }
        return $sIntroText;
    }

    /**
     * Returns short text (part before <cut>)
     *
     * @return mixed
     */
    public function getTextShort() {

        $sText = parent::getTextShort();
        if (Config::Get('plugin.topicintro.introtext.enable') && (!$sText || $sText == $this->getText()) && Config::Get('plugin.topicintro.introtext.text_short')) {
            $sIntroText = $this->getIntroText('');
            if ($sIntroText) {
                $sText = $sIntroText;
            }
        }
        return $sText;
    }

}

// EOF