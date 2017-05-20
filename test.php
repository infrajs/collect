<?php
use infrajs\router\Router;
use infrajs\ans\Ans;
use infrajs\collect\Collect;

$js = Collect::js();

$ans = array();

if(!$js) return Ans::err($ans);
else return Ans::ret($ans);

