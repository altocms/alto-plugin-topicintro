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

    public function setPreviewImage($data, $bAutopreview = false) {

        if (is_null($data) && (func_num_args() == 1)) {
            $bAutopreview = null;
        }
        $this->setExtraValue('preview_image', $data);
        $this->setAutopreview($bAutopreview);
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

    protected function _ignoreByCssClasses($aClasses, $aSrcClasses) {

        if (!is_array($aClasses)) {
            $aClasses = explode(' ', $aClasses);
        }
        if (!is_array($aSrcClasses)) {
            $aSrcClasses = explode(' ', $aSrcClasses);
        }

        if (sizeof($aClasses) == sizeof($aSrcClasses)) {
            if (sizeof($aClasses) == 1) {
                return reset($aClasses) == reset($aSrcClasses);
            }
            sort($aClasses);
            sort($aSrcClasses);
            foreach ($aClasses as $iKey => $sCssClass) {
                if ($aSrcClasses[$iKey] != $sCssClass) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    protected function _ignoreByImageSize($aSize, $sImagePath) {
        
        if (strlen($sImagePath) > 3) {
            if ($sImagePath[0] === '/' && $sImagePath[1] !== '/') {
                $sImagePath = ALTO_DIR . $sImagePath;
            }
            $aInfo = @getimagesize($sImagePath);
            if ($aInfo && $aInfo[0] >= $aSize[0] && $aInfo[1] >= $aSize[1]) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * @param string $sText
     * @param array  $aParams
     *
     * @return mixed
     */
    protected function _seekProtoImages($sText = null, $aParams = array()) {

        $aResult = array();
        if (is_null($sText)) {
            $sText = $this->getText();
        }

        // Seek all images and select the first with no data:URI
        if (preg_match_all('~\<img\s[^>]*src\s*=\s*[\'\"]?([^\s\'\"]+)[\'\"]?\s*[^>]*\>~si', $sText, $aM, PREG_OFFSET_CAPTURE)) {
            if (isset($aParams['ignore']['css_class'])) {
                $aIgnoreCssClasses = $aParams['ignore']['css_class'];
                if (!is_array($aIgnoreCssClasses)) {
                    $aIgnoreCssClasses = array((string)$aIgnoreCssClasses);
                }
            } else {
                $aIgnoreCssClasses = array();
            }
            $sMinWidth = $sMinHeight = null; 
            if ($aParams['ignore']['size']) {
                if (strpos($aParams['ignore']['size'], 'x') !== false) {
                    list($sMinWidth, $sMinHeight) = explode('x', $aParams['ignore']['size']);
                } else {
                    $sMinWidth = $sMinHeight = $aParams['ignore']['size'];
                }
            }
            foreach ($aM[1] as $nIdx => $aData) {
                // $aM[1][x][0] - link to image or data:URI
                $sImg = trim($aData[0]);
                if ($sImg && strpos($sImg, 'data:') === false) {
                    // $aM[0][x][0] - tag <img ...>
                    if ($aIgnoreCssClasses && preg_match('/\sclass\s*=\s*[\'\"]([^\'\"]+)[\'\"]/si', $aM[0][$nIdx][0], $aMtch)) {
                        $bIgnore = $this->_ignoreByCssClasses($aIgnoreCssClasses, $aMtch[1]);
                    } else {
                        $bIgnore = false;
                    }
                    if (!$bIgnore && $sMinWidth && $sMinHeight) {
                        $bIgnore = $this->_ignoreByImageSize(array($sMinWidth, $sMinHeight), $sImg);
                    }
                    
                    if (!$bIgnore) {
                        // $aM[0][x][1] - found position
                        $aResult[$aM[0][$nIdx][1]] = $sImg;
                        break;
                    }
                }
            }
        }

        if (Config::Get('plugin.topicintro.autopreview.video')) {
            $aData = $this->PluginTopicintro_ModuleVideoinfo_ParseText($sText);
            if ($aData) {
                foreach($aData as $aVideoInfo) {
                    if (isset($aVideoInfo['info']['thumbnail'])) {
                        $aResult[$aVideoInfo['pos']] = $aVideoInfo['info']['thumbnail'];
                    }
                }
            }
        }

        return $aResult;
    }

    /**
     * @param bool $bInIntroText
     *
     * @return string|null
     */
    public function getFirstImage($bInIntroText = false) {

        $sPropKey = '_first_image_url_' . ($bInIntroText ? 'i' : 't');
        $sImg = $this->getProp($sPropKey);
        if (is_null($sImg)) {
            $sImg = '';
            if ($bInIntroText) {
                $sText = $this->getIntroText('');
            } else {
                $sText = $this->getText();
            }
            $aParams = Config::Get('plugin.topicintro.autopreview');
            if ($aImg = $this->_seekProtoImages($sText, $aParams)) {
                if (sizeof($aImg) > 1) {
                    ksort($aImg);
                }
                $sImg = reset($aImg);
            }
            $this->setProp($sPropKey, $sImg);
        }
        return $sImg;
    }

    /**
     * @return string
     */
    public function getPreviewImage() {

        $sPreviewImage = $this->getExtraValue('preview_image');
        $iPhotosetCover = $this->getPhotosetMainPhotoId();
        if ($iPhotosetCover) {
            return null;
        }
        if (is_null($sPreviewImage)) {
            if ($nId = $this->getPhotosetMainPhotoId()) {
                $oTopicPhoto = $this->Topic_GetTopicPhotoById($nId);
                if ($oTopicPhoto) {
                    $sPreviewImage = $oTopicPhoto->getUrl();
                }
            }
            if (!$sPreviewImage && Config::Get('plugin.topicintro.autopreview.enable')) {
                $sPreviewImage = $this->getFirstImage();
                $this->setAutoPreviewImage($sPreviewImage ? $sPreviewImage : false);
            } else {
                $this->setPreviewImage($sPreviewImage ? $sPreviewImage : false);
            }
            if (Config::Get('plugin.topicintro.autopreview.autosave') && $this->getId()) {
                $this->Topic_UpdateTopic($this);
            }
        }
        return $sPreviewImage;
    }

    /**
     * Normalize image size format using predefined settings
     *
     * @param string|int $xSize
     *
     * @return string
     */
    protected function _normalizePreviewSize($xSize = null) {

        if (!$xSize) {
            $xSize = Config::Get('plugin.topicintro.preview.size.default');
            if (!$xSize) {
                $xSize = self::DEFAULT_PREVIEW_SIZE;
            }
        } elseif ($xPresetSize = Config::Get('plugin.topicintro.preview.size.' . $xSize)){
            $xSize = $xPresetSize;
        }
        if (is_numeric($xSize) && intval($xSize) == $xSize) {
            if (Config::Get('plugin.topicintro.single_width')) {
                $xSize = 'x' . $xSize;
            } else {
                $xSize = $xSize . 'x' . $xSize;
            }
        }
        return (string)$xSize;
    }

    /**
     * @param string|int $xSize
     *
     * @return string|null
     */
    public function getPreviewImageUrl($xSize = null) {

        if ($sUrl = $this->getPreviewImage()) {
            if (F::File_IsLocalUrl($sUrl)) {
                $sSize = $this->_normalizePreviewSize($xSize);
                $sModSuffix = F::File_ImgModSuffix($sSize, strtolower(pathinfo($sUrl, PATHINFO_EXTENSION)));
                $sUrl = $sUrl . $sModSuffix;
                if (Config::Get('module.image.autoresize')) {
                    $sFile = $this->Uploader_Url2Dir($sUrl);
                    if (!F::File_Exists($sFile)) {
                        $this->Img_Duplicate($sFile);
                    }
                    $this->_setPreviewImageSize($sSize, $sFile);
                }
            }
            return $sUrl;
        } else {
            return null;
        }
    }

    /**
     * Set preview image sizes and attributes and return settings as array
     *
     * @param string      $sSize
     * @param string|null $sFile
     * @param bool        $bReset
     *
     * @return array
     */
    protected function _setPreviewImageSize($sSize, $sFile = null, $bReset = false) {

        $sPropKey = '_size-' . $sSize . '-imgsize';
        $aSize = $this->getProp($sPropKey);
        if (!$aSize || $bReset) {
            if ($sFile && F::File_Exists($sFile)) {
                // real sizes
                $aSize = getimagesize($sFile);
                $aSize['width'] = $aSize[0];
                $aSize['height'] = $aSize[1];
                $aSize['attr'] = $aSize[3];
                $aSize['style'] = ''
                    . ($aSize[0] ? 'width:' . $aSize[0] . 'px;' : '')
                    . ($aSize[1] ? 'height:' . $aSize[1] . 'px;' : '');
            } else {
                // computed sizes
                $aModAttr = F::File_ImgModAttr($sSize);
                $aSize = array(
                    'width'  => $aModAttr['width'],
                    'height' => $aModAttr['height'],
                    'attr'   => ' '
                        . ($aModAttr['width'] ? 'width="' . $aModAttr['width'] . '"' : '') . ' '
                        . ($aModAttr['height'] ? 'height="' . $aModAttr['height'] . '"' : '') . ' ',
                    'style' => null,
                );
                if (!empty($aModAttr['mod'])) {
                    if ($aModAttr['mod'] == 'fit') {
                        $aSize['max-width'] = $aModAttr['width'];
                        $aSize['max-height'] = $aModAttr['height'];
                        $aSize['style'] = ''
                            . ($aSize['width'] ? 'max-width:' . $aSize['width'] . 'px;' : '')
                            . ($aSize['height'] ? 'max-height:' . $aSize['height'] . 'px;' : '');
                    }
                    if ($aModAttr['mod'] == 'pad') {
                        $aSize['min-width'] = $aModAttr['width'];
                        $aSize['min-height'] = $aModAttr['height'];
                        $aSize['style'] = ''
                            . ($aSize['width'] ? 'min-width:' . $aSize['width'] . 'px;' : '')
                            . ($aSize['height'] ? 'min-height:' . $aSize['height'] . 'px;' : '');
                    }
                }
                if (!$aSize['style']) {
                    $aSize['style'] = ''
                        . ($aSize['width'] ? 'width:' . $aSize['width'] . 'px;' : '')
                        . ($aSize['height'] ? 'height:' . $aSize['height'] . 'px;' : '');
                }
            }
            $this->setProp($sPropKey, $aSize);
        }
        return $aSize;
    }

    /**
     * Returns preview image's sizes and attributes
     *
     * @param string|int $xSize
     *
     * @return array
     */
    public function getPreviewImageSize($xSize = null) {

        $sSize = $this->_normalizePreviewSize($xSize);
        $sPropKey = '_size-' . $sSize . '-imgsize';
        $aSize = $this->getProp($sPropKey);
        if (!$aSize) {
            $aSize = $this->_setPreviewImageSize($sSize);
        }
        return $aSize;
    }

    /**
     * Returns preview image's sizes as CSS style values
     *
     * @param string|int $xSize
     *
     * @return string
     */
    public function getPreviewImageSizeStyle($xSize = null) {

        $aSize = $this->getPreviewImageSize($xSize);
        return !empty($aSize['style']) ? $aSize['style'] : '';
    }

    /**
     * Returns preview image's sizes as HTML tag attributes
     *
     * @param string|int $xSize
     *
     * @return string
     */
    public function getPreviewImageSizeAttr($xSize = null) {

        $aSize = $this->getPreviewImageSize($xSize);
        return !empty($aSize['attr']) ? $aSize['attr'] : '';
    }

    /* Intro text */

    /**
     * Set intro text for this topic
     *
     * @param string $sData
     */
    public function setIntroText($sData) {

        $this->setExtraValue('text_intro', $sData);
    }

    /**
     * Returns intro text (announce)
     *
     * @param string $sPostfix
     * @param bool   $bIgnoreShortText
     *
     * @return string
     */
    public function getIntroText($sPostfix = '...', $bIgnoreShortText = false) {

        $sIntroText = $this->getExtraValue('text_intro');
        if (!$sIntroText && !$bIgnoreShortText && Config::Get('plugin.topicintro.introtext.text_short')) {
            $sIntroText = parent::getTextShort();
            if (!Config::Get('plugin.topicintro.introtext.html_tags')) {
                $sIntroText = strip_tags($sIntroText);
            }
            $sIntroText = trim($sIntroText);
        }
        if (!$sIntroText && Config::Get('plugin.topicintro.introtext.autocreate')) {
            $sIntroText = $this->Topic_ParseIntroText($this->getText());
            $nMax = intval(Config::Get('plugin.topicintro.introtext.max_size'));
            $nLen = mb_strlen($sIntroText, 'UTF-8');
            if ($nMax && $nLen > $nMax) {
                $sIntroText = $this->Text_TruncateText($sIntroText, $nMax - mb_strlen($sPostfix, 'UTF-8'));
            }
        }
        return $sIntroText;
    }

    /**
     * Returns short text (part before <cut>)
     *
     * @return string
     */
    public function getTextShort() {

        $sText = parent::getTextShort();
        if (Config::Get('plugin.topicintro.introtext.enable') && (!$sText || $sText == $this->getText()) && Config::Get('plugin.topicintro.introtext.text_short')) {
            $sIntroText = $this->getIntroText('', true);
            if ($sIntroText) {
                $sText = $sIntroText;
            }
        }
        return $sText;
    }

}

// EOF