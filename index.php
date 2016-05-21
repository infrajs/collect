<?php
namespace infrajs\collect;
use infrajs\access\Access;
use infrajs\load\Load;
use infrajs\mem\Mem;
use infrajs\ans\Ans;
use infrajs\nostore\Nostore;
use MatthiasMullie\Minify;
use infrajs\router\Router;

if (!is_file('vendor/autoload.php')) {
	chdir('../../../');
	require_once('vendor/autoload.php');
	Router::init();
}

Nostore::pubStat(); //Кэшируется, если public разрешён, как статика, надолго

if(isset($_GET['js'])) $isjs = 'js';
else if(isset($_GET['css'])) $isjs = '';
else die('Необходимо добавить параметр css или js');

header('Infrajs-Cache: true');

$re = isset($_GET['re']); //Modified re нужно обновлять с ctrl+F5
$debug = Access::debug();
$name = Ans::GET('name','string','');

if ($debug || $re) {
	header('Infrajs-Cache: false');
	
	if ($isjs) $code = Collect::js($name);
	else $code = Collect::css($name);

	$key = 'Infrajs::Collect::' . $isjs . true;//Кэш с zip
	Mem::delete($key);
	$key = 'Infrajs::Collect::' . $isjs . false;//Кэш без zip
	Mem::delete($key);

	if ($isjs) header('Content-Type: text/javascript; charset=utf-8');
	else header('Content-Type: text/css; charset=utf-8');
	
	echo $code;
	exit;
}

$p = explode(',', str_replace(' ', '', $_SERVER['HTTP_ACCEPT_ENCODING']));
$isgzip = in_array('gzip', $p);

$key = 'Infrajs::Collect::' . $isjs . $isgzip; //Два кэша зазипованый и нет. Не все браузеры понимают зазипованую версию.

$code = Mem::get($key);

if (!$code) {
	header('Infrajs-Cache: false');

	if ($isjs) $code = Collect::js($name);
	else $code = Collect::css($name);
	
	if ($isjs) $min = new Minify\JS($code);
	else $min = new Minify\CSS($code);
	
	if ($isgzip) {	
		$code = $min->gzip();
	} else {
		$code = $min->minify();
	}
	Mem::set($key, $code);
}

if ($isgzip) {
	header('Content-Encoding: gzip');
	header('Vary: accept-encoding');
	header('Content-Length: ' . strlen($code));
}

if ($isjs) header('Content-Type: text/javascript; charset=utf-8');
else header('Content-Type: text/css; charset=utf-8');

echo $code;
