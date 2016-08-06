<?php
/**
 * This is test suite for Meow! Links, product for vBulletin.
 *
 * PHP version 5
 *
 * @category vBulletin
 * @package  Meow!_Links
 * @author   Catlord Meow! <no@grumpy.cat>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3
 * @version  1.0
 * @date     2016-08-03
 * @link     https://github.com/CatlordMeow/Misc
 */

$messagetext = <<<EOT
[noparse]
[url]https://www.youtube.com/[/url]
_http://ya.ru/
[/noparse]
http://s008.radikal.ru/i304/1607/fd/99e3936bca32.jpg
_http://php.net/
_http://php.net:666/
EOT;

$skiptaglist = 'url|email|code|php|html|noparse';
$vbulletin->options['meow_ref_buffers'] = 10;
$vbulletin->options['meow_ref_encoding'] = '';
$vbulletin->options['meow_ref_replace'] = true;
$vbulletin->options['meow_pic_detect'] = true;
$vbulletin->options['meow_pic_exts'] = 'jpg . gif png';
$vbulletin->options['meow_vid_code'] = 'video';
$vbulletin->options['meow_vid_detect'] = true;
$stylevar['charset'] = 'utf-8';

/* ====================================
    place your just below this line  */

/* place your code just above this line
======================================== */
	
	print $messagetext;

?>
