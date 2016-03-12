<?php
namespace infrajs\collect;
use infrajs\access\Access;
use infrajs\load\Load;
use infrajs\config\Config;
use infrajs\mem\Mem;
use infrajs\nostore\Nostore;
use MatthiasMullie\Minify;

if (!is_file('vendor/autoload.php')) {
	chdir('../../../');
	require_once('vendor/autoload.php');
	Config::init();
}

Nostore::pubStat(); //Кэшируется, если public разрешён, как статика, надолго

header('Infrajs-Cache: true');
$re = isset($_GET['re']); //Modified re нужно обновлять с ctrl+F5
$debug = Access::debug();
if ($debug || $re) {
	$js = Collect::js();
	$key = 'Infrajs::Config::js'.true;

	Mem::delete($key);
	$key = 'Infrajs::Config::js'.false;
	Mem::delete($key);
	header('Content-Type: text/javascript; charset=utf-8');
	echo $js;
	exit;
}



$p = explode(',', str_replace(' ', '', $_SERVER['HTTP_ACCEPT_ENCODING']));
$isgzip = in_array('gzip', $p);

$key = 'Infrajs::Config::js'.$isgzip; //Два кэша зазипованый и нет. Не все браузеры понимают зазипованую версию.

$js = Mem::get($key);

if (!$js) {
	$js = Collect::js();
	if ($isgzip) {
		$min = new Minify\JS($js);
		$js = $min->gzip();
	} else {
		$min = new Minify\JS($js);
		$js = $min->minify();
	}
	Mem::set($key, $js);
}
if ($isgzip) {
	header('Content-Encoding: gzip');
	header('Vary: accept-encoding');
	header('Content-Length: ' . strlen($js));
}
header('Content-Type: text/javascript; charset=utf-8');
echo $js;
