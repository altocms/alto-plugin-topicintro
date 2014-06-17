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

    /**
     * @return string
     */
    public function getFirstImage() {

        $sImg = $this->getProp('_first_image_url');
        if (is_null($sImg)) {
            $sImg = '';
            if (preg_match('/\<img\s.*src\s*=\s*([^\s]+)\s*\/\>/siU', $this->getText(), $aM, PREG_OFFSET_CAPTURE)) {
                if (isset($aM[1][0])) {
                    $sImg = trim($aM[1][0]);
                    if (substr($sImg, 0, 1) == '"' || substr($sImg, 0, 1) == '\'') {
                        $sImg = substr($sImg, 1, strlen($sImg) - 2);
                    }
                }
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
            $this->setExtraValue('preview_image', $sPreviewImage ? $sPreviewImage : false);
            $this->setExtraValue('preview_image_is_auto', true);
            if (Config::Get('plugin.topicintro.autopreview.enable')) {
                $this->Topic_UpdateTopic($this);
            }
        }
        return $sPreviewImage;
    }


    public function getPreviewImageUrl($xSize = null) {

        if ($sUrl = $this->getPreviewImage()) {
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
            return $sUrl;
        } else {
            return null;
        }
    }


}

// EOF