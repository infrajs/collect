<?php
namespace infrajs\collect;
use infrajs\router\Router;
use infrajs\ans\Ans;

if (!is_file('vendor/autoload.php')) {
	chdir('../../../');
	require_once('vendor/autoload.php');
	Router::init();
}

$js = Collect::js();

$ans = array();

if(!$js) return Ans::err($ans);
else return Ans::ret($ans);

