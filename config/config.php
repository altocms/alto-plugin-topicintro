<?php
/*---------------------------------------------------------------------------
 * @Project: Alto CMS
 * @Project URI: http://altocms.com
 * @Description: Advanced Community Engine
 * @Copyright: Alto CMS Team
 * @License: GNU GPL v2 & MIT
 *----------------------------------------------------------------------------
 */

/*
 * Разрешить автоматическое создание превью изображений к топику
 */
$config['autopreview']['enable'] = true;

/*
 * Создавать автопревью из видео
 */
$config['autopreview']['video'] = true;

/*
 * Автоматически сохранять автопревью в топиках
 * (иначе оно будет создаваться каждый раз)
 */
$config['autopreview']['autosave'] = true;

/*
 * Хук, по которому выводится автопревью
 * (если не требуется, то закомментируйте эту строку)
 */
$config['autopreview']['hook_list'] = 'template_topic_content_begin';
//$config['autopreview']['hook_show'] = 'template_topic_content_begin';

/*
 * Предзаданные размеры автопревью
 */
$config['preview_size'] = array(
    'default' => 'x300', // размер по умолчанию
);

/*
 * Как интерпретировать одиночное значение
 *   true:  300 -> 'x300'       - LS compatibility
 *   false  300 -> '300x300'    - Alto mode
 */
$config['single_width'] = true;

/*
 * Разрешить анонсы топиков (интротекст)
 */
$config['introtext']['enable'] = false;

/*
 * Максимальный размер текста анонса
 */
$config['introtext']['max_size'] = 200;

/*
 * Создавать интротекст автоматически из основного текста
 */
$config['introtext']['autocreate'] = true;

/*
 * Использовать интротекст, если краткий текст топика не задан
 */
$config['introtext']['text_short'] = true;

$config['introtext']['hook_list'] = 'template_topic_content_begin';

//$config['introtext']['hook_show'] = 'template_topic_content_begin';

// EOF