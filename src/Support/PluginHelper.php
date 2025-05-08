<?php
/*
* Define your helper functions in this file to be used globally
*/


if (!function_exists('plugin_path')) {
	/**
	 * 获取插件目录
	 * @param string $fileName
	 * @return string
	 *
	 * @author: hongbinwang
	 * @time  : 2023/8/21 17:37
	 */
	function plugin_path(string $fileName = ''): string
	{
		return base_path('plugins'.DIRECTORY_SEPARATOR.$fileName);
	}
}

if (!function_exists('plugin_config')) {
	/**
	 * 获取插件配置
	 * @param string $pluginName
	 * @return string
	 *
	 * @author: hongbinwang
	 * @time  : 2023/8/21 17:37
	 */
	function plugin_config(string $pluginName = ''): string
	{
		$plugin = app('plugins')->find($pluginName);
		return $plugin->getConfig();
	}
}

if (!function_exists('plugin_cover')) {
	/**
	 * 获取插件封面图
	 * @param string $name
	 * @return string
	 *
	 * @author: hongbinwang
	 * @time  : 2023/8/21 17:37
	 */
    function plugin_cover(string $name = ''): string
    {
	    $text = format_cover_text($name);
	    $text = mb_substr(strtoupper($text), 0, 4);
	    $total = unpack('L', hash('adler32', $text, true))[1];
	    $hue = $total % 360;
	    list($r, $g, $b) = hsv2rgb($hue / 360, 0.3, 0.9);
	    $background = "rgb({$r},{$g},{$b})";
	    $icon = <<<EOT
 <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="100px" height="100px" viewBox="0 0 512 512" xml:space="preserve">
    <circle cx="250" cy="250" r="230" style="fill:{$background};"/>
    <text x="50%" y="50%" text-anchor="middle" dominant-baseline="middle"
     font-size="160" font-family="Verdana, Helvetica, Arial, sans-serif" fill="white" >{$text}</text>
</svg>
EOT;
	    return $icon;
    }
}

if (!function_exists('format_cover_text')) {
	/**
	 * 格式化生成文本头像的字符串
	 * @param $str
	 * @return false|mixed|string
	 *
	 * @author: hongbinwang
	 * @time  : 2023/8/21 17:37
	 */
	function format_cover_text($str): mixed
	{
		$len = strlen($str);

		// 传入字符串为中文
		if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str)) {
			return mb_substr($str, 0, 1);
		}

		// 传入字符串为纯英文
		if (preg_match('/^[A-Za-z]+$/', $str)) {
			return strtoupper(substr($str, 0, 2));
		}

		// 传入字符串是其他字符（非中文、非英文）
		if ($len > 2) {
			return substr($str, 0, 2);
		}

		return $str;
	}
}

if (!function_exists('hsv2rgb')) {
	function hsv2rgb($h, $s, $v)
	{
		$r = $g = $b = 0;

		$i = floor($h * 6);
		$f = $h * 6 - $i;
		$p = $v * (1 - $s);
		$q = $v * (1 - $f * $s);
		$t = $v * (1 - (1 - $f) * $s);

		switch ($i % 6) {
			case 0:
				$r = $v;
				$g = $t;
				$b = $p;
				break;
			case 1:
				$r = $q;
				$g = $v;
				$b = $p;
				break;
			case 2:
				$r = $p;
				$g = $v;
				$b = $t;
				break;
			case 3:
				$r = $p;
				$g = $q;
				$b = $v;
				break;
			case 4:
				$r = $t;
				$g = $p;
				$b = $v;
				break;
			case 5:
				$r = $v;
				$g = $p;
				$b = $q;
				break;
		}

		return [
			floor($r * 255),
			floor($g * 255),
			floor($b * 255)
		];
	}

}