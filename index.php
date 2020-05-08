<?php

namespace infrajs\collect;

use infrajs\access\Access;
use infrajs\load\Load;
use infrajs\mem\Mem;
use infrajs\ans\Ans;
use infrajs\nostore\Nostore;
use MatthiasMullie\Minify;
use infrajs\router\Router;
use infrajs\config\Config;
use akiyatkin\fs\FS;
use infrajs\rest\Rest;
use infrajs\path\Path;
use infrajs\config\search\Search;

Nostore::pub();

$code = Rest::get(function () {
	echo '/js или /css';
}, 'js', function () {
	$srcs = [];
	Config::run( function ($name, $conf) use (&$srcs){
		if (empty($conf['js'])) return;
		$jss = $conf['js'];
		if (!is_array($jss)) $jss = [$jss];
		foreach ($jss as $js) {
			$src = Path::theme('-'.$name.'/'.$js);
			if (Path::getExt($src) == 'php') $src = '-'.$name.'/'.$js;
			$srcs[] = $src;
		}
	});
	header("Content-Type: application/javascript");
	$html = '';
	$i = 0;
	foreach ($srcs as $src) {
		$i++;
		//$html .= "import t".$i." from '/".$src."'\n";
		$html .= "import { } from '/".$src."'\n";
	}
	return $html;
}, 'css', function () {
	$srcs = [];
	Config::run( function ($name, $conf) use (&$srcs){
		if (empty($conf['css'])) return;
		$jss = $conf['css'];
		if (!is_array($jss)) $jss = [$jss];
		foreach ($jss as $js) {
			$src = Path::theme('-'.$name.'/'.$js);
			if (Path::getExt($src) == 'php') $src = '-'.$name.'/'.$js;
			$srcs[] = $src;
		}
	});
	header("Content-Type: text/css");
	$html = '';
	foreach ($srcs as $src) {
		$html .= "\n\n".'/*load css '.$src."*/\r\n";
		$html .= Load::loadTEXT('-csspath/?src='.$src);
	}
	return $html;
});
echo $code;
return;

$time = Ans::GET('time', 'string', '');
if ($time) Nostore::pubStat(); //Кэшируется, если public разрешён, как статика, надолго. Если указана версия

if (isset($_GET['js'])) $isjs = 'js';
else if (isset($_GET['css'])) $isjs = '';
else die('Необходимо добавить параметр css или js');

if (!Load::isphp()) {
	header('Collect-Cache: true');
}

$re = isset($_GET['re']); //Modified re нужно обновлять с ctrl+F5
$debug = Access::debug();
$name = Ans::GET('name', 'string', '');

if ($debug || $re) {
	if (!Load::isphp()) header('Collect-Cache: false');

	if ($isjs) $code = Collect::js($name);
	else $code = Collect::css($name);

	$key = 'Collect::Collect::' . $isjs . true . $time; //Кэш с zip
	Mem::delete($key);
	$key = 'Collect::Collect::' . $isjs . false . $time; //Кэш без zip
	Mem::delete($key);

	if ($isjs) return Ans::js($code);
	return Ans::css($code);
}

$isgzip = false;
/*if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
	$p = explode(',', str_replace(' ', '', $_SERVER['HTTP_ACCEPT_ENCODING']));
	$isgzip = !Load::isphp()&&in_array('gzip', $p);
}*/

$key = 'Collect::Collect::' . $isjs . $isgzip . $time; //Два кэша зазипованый и нет. Не все браузеры понимают зазипованую версию.

$data = Mem::get($key);

if (!is_array($data)) $data['code'] = '';

$code = $data['code'];

if (!$code) {

	if (!Load::isphp()) header('Collect-Cache: false');


	if ($isjs) $code = Collect::js($name);
	else $code = Collect::css($name);

	/*if ($isjs) $min = new Minify\JS($code);
	else $min = new Minify\CSS($code);


	if ($isgzip) {	
		$code = $min->gzip();
	} else {
		$code = $min->minify();
	}*/
	Mem::set($key, array('code' => $code));
}

/*if (!Load::isphp()) {
	if ($isgzip) {
		header('Content-Encoding: gzip');
		header('Vary: accept-encoding');
		header('Content-Length: ' . strlen($code));
	}
}*/
if ($isjs) return Ans::js($code);
return Ans::css($code);
