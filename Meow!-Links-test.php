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
 * @date     2016-08-06
 * @link     https://github.com/CatlordMeow/Misc
 */

$messagetext = <<<EOT
[noparse]
[url]https://www.youtube.com/[/url]
_http://ya.ru/
[/noparse]
https://www.youtube.com/watch?v=0TFmncOtzcE
www.youtube.com/watch?v=0TFmncOtzcE
https://youtube.com/watch?v=0TFmncOtzcE
youtube.com/watch?v=0TFmncOtzcE
EOT;

$skiptaglist = 'url|email|code|php|html|noparse';
$vbulletin->options['meow_ref_buffers'] = 10;
$vbulletin->options['meow_ref_encoding'] = '';
$vbulletin->options['meow_ref_replace'] = true;
$vbulletin->options['meow_pic_detect'] = true;
$vbulletin->options['meow_pic_exts'] = 'jpg . gif png';
$vbulletin->options['meow_vid_code'] = 'video';
$vbulletin->options['meow_vid_detect'] = true;
$stylevar['charset'] = 'windows-1251';



/* ====================================
    place your just below this line  */

if (!function_exists('meow_url_to_bbcode')) {
	function meow_dl_title($text) {
		global $vbulletin;

		if (!$vbulletin->options['meow_ref_replace']) { return null; }

		$options = array(
			'http' => array(
				'user_agent' => 'vBulletin via PHP',
				'max_redirects' => 10,
				'timeout' => 20
			)
		);
		$context = stream_context_create($options);

		if (strtolower($text[2]) == 'www.') {
			$text[4] = $text[2] . $text[4];
			$text[2] = 'http://';
		}
		$page = '';
		$r = $vbulletin->options['meow_ref_buffers'];
		$x = 0;
		if ($fp = @fopen($text[2] . $text[4], 'r', false, $context)) {
			while (!feof($fp) AND ($x < $r)) {
				$page .= fread($fp, 8192);
				$x++;
			}
			@fclose($fp);
		}
		if ($page == '' or !preg_match('#<title>(.*)</title>#is', $page, $m) or
			!$m[1]) {
			return null;
		}
		$title = $m[1];

		// vB 3.x: global $stylevar; $charset = $stylevar['charset'];
		// vB 4.x: vB_Template_Runtime::fetchStyleVar('charset');
		$charset = $vbulletin->options['meow_ref_encoding'];
		if ($charset == '') {
			if (class_exists('vB_Template_Runtime')) {
				$charset = vB_Template_Runtime::fetchStyleVar('charset');
			} else {
				global $stylevar;
				$charset = $stylevar['charset'];
			}
		}
		if ($charset == '') {
			$er = 'Cannot detect encoding, falling back to utf-8' . PHP_EOL;
			print $er;
			trigger_error($er, E_USER_WARNING);
			$charset = 'utf-8';
		}
		$local_chset = strtolower($charset);

		$remote_chset = '';
		$i = count($http_response_header);
		// reverse order! important!
		while ($i > 0) {
			$i--;
			$s = $http_response_header[$i];
			if (!preg_match ('#charset="?([-a-z0-9_]+)#i', $s, $m)) { continue; }
			$remote_chset = $m[1];
			break;
		}
		//print "*** $remote_chset" . PHP_EOL;
		if (!$remote_chset and preg_match('#charset="?([-a-z0-9_]+)#i', $page,
			$m)) {
			$remote_chset = $m[1];
		}
		$remote_chset = $remote_chset ? strtolower($remote_chset) : 'utf-8';
		
		if ($local_chset !== $remote_chset) {
			//print "*** $remote_chset => $local_chset" . PHP_EOL;
			$title = iconv($remote_chset, $local_chset . '//ignore', $title);
		}
		return trim(str_replace('[/url]', '', preg_replace
			("#[\r\n\t]#i", '', html_entity_decode($title))));
	}

	function meow_add_code_to_url($text) {
		global $vbulletin;
		$t = $text[0];

		if ($vbulletin->options['meow_pic_detect']) {
			$x = strrchr($t, '.');
			if ($x !== false and $x !== '.') {
				$x = substr($x, 1);
				$pics = explode(' ', strtolower($vbulletin->options['meow_pic_exts']));
				for ($j = count($pics) - 1; $j >= 0; $j--) {
					$s = $pics[$j];
					if ($s === '.') {
						unset($pics[$j]);
						// print 'dot is extension\r\n' . PHP_EOL;
						continue;
					}
					if ($s[0] === '.') {
						// print 'dot is first char of extension\r\n' . PHP_EOL;
						$pics[$j] = substr($s, 1);
					}
				}
				if (in_array(strtolower($x), $pics, true)) {
					return "[img]{$t}[/img]";
				}
			}
		}

		var_dump($text);
		if ($vbulletin->options['meow_vid_detect']) {
			$youTubeTest = '#^((www\.)?youtube\.com/watch\?.*v=.+|' .
				'youtu\.be/.+)$#i';
			if (preg_match($youTubeTest, $text[4])) {
				$v = $vbulletin->options['meow_vid_code'];
				return "[$v]{$t}[/$v]";
			}
		}

		$x = meow_dl_title($text);
		if ($x == '') {
			$x = $t;
		}
		return "[url=$t]{$x}[/url]";
	}

	function meow_url_to_bbcode($messagetext, $prepend) {
		// the auto parser - adds [url] tags around neccessary things
		$messagetext = str_replace('\"', '"', $messagetext);
		$prepend     = str_replace('\"', '"', $prepend);

		static $urlSearchArray, $emailSearchArray, $emailReplaceArray;
		if (empty($urlSearchArray)) {
			$taglist = '\[b|\[i|\[u|\[left|\[center|\[right|\[indent|\[quote|\[' .
				'highlight|\[\*|\[/b|\[/i|\[/u|\[/left|\[/center|\[/right|\[/' .
				'indent|\[/quote|\[/highlight';
			$urlSearchArray = array(
				'#(^|(?<=[^_a-z0-9-=\]"\'/@]|(?<=' . $taglist .
				')\]))((https?|ftp|gopher|news|telnet)://|www\.)((\[(?!/)|[^\s[^$`"' .
				'\'|{}<>])+)(?!\[/url|\[/img)(?=[,.!)]*(\)\s|\)$|[\s[]|$))#siU'
			);
			$emailSearchArray = array(
				'/([ \n\r\t])([_a-z0-9-]+(\.[_a-z0-9-]+)*@[^\s]+(\.[a-z0-9-]+)*(\.' .
				'[a-z]{2,4}))/si',
				'/^([_a-z0-9-]+(\.[_a-z0-9-]+)*@[^\s]+(\.[a-z0-9-]+)*(\.[a-z]' .
					'{2,4}))/si'
			);
			$emailReplaceArray = array(
				'\\1[email]\\2[/email]',
				'[email]\\0[/email]'
			);
		}

		$text = preg_replace_callback($urlSearchArray, 'meow_add_code_to_url',
			$messagetext);

		if (strpos($text, '@')) {
			$text = preg_replace($emailSearchArray, $emailReplaceArray, $text);
		}
		return $prepend . $text;
	}

	$messagetext = preg_replace_callback(
		"#(^|\[/($skiptaglist)\])(.*(\[($skiptaglist)\]|$))#siU",
		function ($matches) {
			return meow_url_to_bbcode($matches[3], $matches[1]);
		},
		$messagetext
	);
}
/* place your code just above this line
======================================== */

print $messagetext;

?>