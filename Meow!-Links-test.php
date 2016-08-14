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
 * @version  1.1
 * @date     2016-08-14
 * @link     https://github.com/CatlordMeow/Misc
 */

$messagetext = '
[noparse]
[url]https://www.youtube.com/[/url]
_http://ya.ru/
[/noparse]
http://ya.ru/
http://i045.radikal.ru/1607/8f/57aa834c69de.jpg';

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

$x = simplexml_load_file('Meow!-Links.xml', null, LIBXML_NOCDATA);
eval($x->plugins->plugin->phpcode);

print $messagetext;

?>