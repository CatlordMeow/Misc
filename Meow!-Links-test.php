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
 * @date     2016-08-10
 * @link     https://github.com/CatlordMeow/Misc
 */

$messagetext = <<<EOT
[noparse]
[url]https://www.youtube.com/[/url]
_http://ya.ru/
[/noparse]

http://i045.radikal.ru/1607/8f/57aa834c69de.jpg
[img]http://i045.radikal.ru/1607/8f/57aa834c69de.jpg[/img]
[img] http://i045.radikal.ru/1607/8f/57aa834c69de.jpg[/img]
[img] http://i045.radikal.ru/1607/8f/57aa834c69de.jpg [/img]
gg
EOT;

$skiptaglist = 'url|email|code|php|html|noparse';
$vbulletin->options['meow_ref_buffers'] = 10;
$vbulletin->options['meow_ref_encoding'] = '';
$vbulletin->options['meow_ref_replace'] = true;
$vbulletin->options['meow_pic_detect'] = true;
$vbulletin->options['meow_pic_exts'] = 'jpg . gif png';
$vbulletin->options['meow_vid_code'] = 'video';
$vbulletin->options['meow_vid_detect'] = true;
$vbulletin->bbcodecache = array();
$stylevar['charset'] = 'windows-1251';

class vBulletinHook {
	public static function fetch_hook() { return false; }
	public static function fetch_hookusage() { return array(); }
}


/* ====================================
    place your just below this line  */


		
/* place your code just above this line
======================================== */

print $messagetext;

?>