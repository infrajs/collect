<?php
namespace infrajs\collect;
use infrajs\config\Config;
use infrajs\ans\Ans;

if (!is_file('vendor/autoload.php')) {
	chdir('../../../');
	require_once('vendor/autoload.php');
	Config::init();
}

$js = Collect::js();

$ans = array();

if(!$js) return Ans::err($ans);
else return Ans::ret($ans);

