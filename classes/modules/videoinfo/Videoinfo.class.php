<?php
/*---------------------------------------------------------------------------
 * @Project: Alto CMS
 * @Project URI: http://altocms.com
 * @Description: Advanced Community Engine
 * @Copyright: Alto CMS Team
 * @License: GNU GPL v2 & MIT
 *----------------------------------------------------------------------------
 */

class PluginTopicintro_ModuleVideoinfo extends Module {

    const DEFAULT_HEIGHT = 315;
    const DEFAULT_WIDTH = 560;

    protected $aRules = array();

    public function Init() {

        $this->aRules[] = array(
            'pattern' => '~<video>.+(www\.|\/\/)youtube\.com\/watch.*[\?&]v=(?<id>[\w\-]+).*</video>~si',
            'service' => 'youtube',
            'callback' => 'GetInfoYoutube',
        );

        $this->aRules[] = array(
            'pattern' => '~<video>.+//youtu\.be/(?<id>[\w\-]+).*</video>~si',
            'service' => 'youtube',
            'callback' => 'GetInfoYoutube',
        );

        $this->aRules[] = array(
            'pattern' => '~<iframe[^>]+src\s*=\s*["\'](https?:)?//(?:www\.|)youtube\.com/embed/(?<id>[\w\-]+).*</iframe>~si',
            'service' => 'youtube',
            'callback' => 'GetInfoYoutube',
        );

        $this->aRules[] = array(
            'pattern' => '~<object.+param[^>]+value\s*=\s*["\'](https?:)?//(?:www\.|)youtube\.com/v/(?<id>[\w\-]+).+</object>~si',
            'service' => 'youtube',
            'callback' => 'GetInfoYoutube',
        );

        $this->aRules[] = array(
            'pattern' => '~<iframe[^>]+src\s*=\s*["\'](https?:)?//(?:www\.|)player\.vimeo\.com/video/(?<id>\d+).*</iframe>~si',
            'service' => 'vimeo',
            'callback' => 'GetInfoVimeo',
        );

        $this->aRules[] = array(
            'pattern' => '~<video>.+/rutube\.ru\/video/(?<id>[\w\-]+).*</video>~si',
            'service' => 'rutube',
            'callback' => 'GetInfoRutube',
        );

        $this->aRules[] = array(
            'pattern' => '~<iframe[^>]+src\s*=\s*["\'](https?:)?//(?:www\.|)rutube\.ru\/play\/embed\/(?<id>\d+)["\'\s\<].*/iframe>~si',
            'service' => 'rutube',
            'callback' => 'GetInfoRutubeTrack',
        );

        $this->aRules[] = array(
            'pattern' => '~<iframe[^>]+src\s*=\s*["\'](https?:)?//(?:www\.|)rutube\.ru\/play\/embed\/(?<id>\w{32}).*</iframe>~si',
            'service' => 'rutube',
            'callback' => 'GetInfoRutube',
        );

        $this->aRules[] = array(
            'pattern' => '~<video>(https?:)?//(?:www\.|)rutube\.ru\/play\/embed\/(?<id>\w+).*</video>~si',
            'service' => 'rutube',
            'callback' => 'GetInfoRutube',
        );
    }

    /**
     * @param $sUrl
     * @param $aPaths
     *
     * @return array
     */
    public function ReadInfoFromXml($sUrl, $aPaths) {

        $aResult = array_fill_keys(array_keys($aPaths), null);
        if ($oXml = @simplexml_load_file($sUrl)) {
            foreach ($aPaths as $sKey => $sPath) {
                $sData = $oXml->xpath($sPath);
                $aResult[$sKey] = trim((string)array_shift($sData));
            }
        } else {
            $aResult = array();
        }
        return $aResult;
    }

    /**
     * @param string $sUrl
     * @param array  $aMap
     *
     * @return array
     */
    public function ReadInfoFromJson($sUrl, $aMap) {

        if (($sData = file_get_contents($sUrl)) && $aData = @json_decode($sData, true)) {
            $aResult = array();
            foreach($aMap as $sKey => $sMap) {
                if (isset($aData[$sMap])) {
                    $aResult[$sKey] = $aData[$sMap];
                } else {
                    $aResult[$sKey] = null;
                }
            }
        } else {
            $aResult = array_fill_keys(array_keys($aMap), null);
        }

        return $aResult;
    }

    /**
     * @param string $sVideoId
     *
     * @return array
     */
    public function GetInfoYoutube($sVideoId) {

        $sUrl = 'http://www.youtube.com/oembed?url=http%3A//youtube.com/watch%3Fv%3D' . $sVideoId . '&format=json';
        $aResult = $this->ReadInfoFromJson($sUrl, array(
            'thumbnail' => 'thumbnail_url',
            'width' => 'thumbnail_width',
            'height' => 'thumbnail_height',
            'html' => 'html',
        ));
        $aResult['link'] = 'http://youtu.be/' . $sVideoId;

        return $aResult;
    }

    /**
     * @param string $sVideoId
     *
     * @return array
     */
    public function GetInfoVimeo($sVideoId) {

        $sUrl = 'http://vimeo.com/api/v2/video/' . $sVideoId . '.xml';
        $aResult = $this->ReadInfoFromXml($sUrl, array(
                'thumbnail' => 'video/thumbnail_large',
                'width' => 'video/width',
                'height' => 'video/height',
                'duration' => 'video/duration',
                'link' => 'video/url',
            ));
        if ($aResult) {
            if ($aResult['width'] && $aResult['height']) {
                $nW = self::DEFAULT_WIDTH;
                $nK = $aResult['width'] / $nW;
                $nH = round($aResult['height'] / $nK);
            } else {
                $nW = self::DEFAULT_WIDTH;
                $nH = self::DEFAULT_HEIGHT;
            }
            $sHtml = '<iframe src="//player.vimeo.com/video/' . $sVideoId . '?title=0&amp;byline=0&amp;portrait=0" width="' . $nW . '" height="' . $nH . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
            $aResult['html'] = $sHtml;
        }
        return $aResult;
    }

    /**
     * @param $sVideoId
     *
     * @return array
     */
    public function GetInfoRutube($sVideoId) {

        $sUrl = 'http://rutube.ru/api/video/' . $sVideoId . '/?format=xml';
        $aResult = $this->ReadInfoFromXml($sUrl, array(
                'thumbnail' => 'thumbnail_url',
                'duration' => 'duration',
                'link' => 'video_url',
                'html' => 'html',
                'track_id' => 'track_id'
            ));
        if ($aResult) {
            $nW = self::DEFAULT_WIDTH;
            $nH = self::DEFAULT_HEIGHT;
            if ($aResult['html'] && preg_match('~\s+width\s*=\s*["\']?(\d+)["\']?\s+height\s*=\s*["\']?(\d+)["\']?~', $aResult['html'], $aM)) {
                $aResult['width'] = $aM[1];
                $aResult['height'] = $aM[2];
            }
            if ($aResult['width'] && $aResult['height']) {
                $nK = $aResult['width'] / $nW;
                $nH = round($aResult['height'] / $nK);
            }
            $sData = @file_get_contents($sUrl);
            if ($sData && preg_match('~<thumbnail_large>(.+)</thumbnail_large>~', $sData, $aM)) {
                $aResult['thumbnail'] = trim($aM[1]);
            }
            $sHtml = '<iframe width="' . $nW . '" height="' . $nH . '" src="//rutube.ru/play/embed/' . $aResult['track_id'] . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>';
            $aResult['html'] = $sHtml;
        }
        return $aResult;
    }

    /**
     * @param $sVideoId
     *
     * @return array
     */
    public function GetInfoRutubeTrack($sVideoId) {

        $sUrl = 'http://rutube.ru/api/oembed/?url=http://rutube.ru/tracks/' . $sVideoId . '.html/&format=xml';
        $aResult = $this->ReadInfoFromXml($sUrl, array(
                'thumbnail' => 'thumbnail_url',
                'duration' => 'duration',
                'link' => 'video_url',
                'width' => 'width',
                'height' => 'height',
                'html' => 'html',
            ));
        if ($aResult) {
            if ($aResult['width'] && $aResult['height']) {
                $nW = self::DEFAULT_WIDTH;
                $nK = $aResult['width'] / $nW;
                $nH = round($aResult['height'] / $nK);
            } else {
                $nW = self::DEFAULT_WIDTH;
                $nH = self::DEFAULT_HEIGHT;
            }
            $sHtml = '<iframe width="' . $nW . '" height="' . $nH . '" src="//rutube.ru/play/embed/' . $sVideoId . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>';
            $aResult['html'] = $sHtml;
        }
        return $aResult;
    }

    /**
     * @param $sText
     *
     * @return array
     */
    public function ParseText($sText) {

        $aResult = array();
        foreach($this->aRules as $aRule) {
            if (preg_match($aRule['pattern'], $sText, $aM, PREG_OFFSET_CAPTURE)) {
                if (isset($aM['id'])) {
                    $sId = $aM['id'][0];
                    $sFunc = $aRule['callback'];
                    $aInfo = $this->$sFunc($sId);
                    $aResult[] = array(
                        'pos' => $aM[0][1],
                        'info' => $aInfo,
                        'service' => $aRule['service'],
                    );
                }
            }
        }
        return $aResult;
    }

}

// EOF