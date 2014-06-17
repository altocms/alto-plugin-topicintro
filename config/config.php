<?php
/*---------------------------------------------------------------------------
 * @Project: Alto CMS
 * @Project URI: http://altocms.com
 * @Description: Advanced Community Engine
 * @Copyright: Alto CMS Team
 * @License: GNU GPL v2 & MIT
 *----------------------------------------------------------------------------
 */

$config['autopreview']['enable'] = true;
$config['autopreview']['save'] = true;
$config['autopreview']['hook'] = 'template_topic_content_begin';

$config['preview_size'] = array(
    'default' => 'x300',
);

/*
 * Как интерпретировать одиночное значение
 *   true:  300 -> 'x300'       - LS compatibility
 *   false  300 -> '300x300'    - Alto mode
 */
$config['single_width'] = true;

// EOF