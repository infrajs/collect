<?php
use akiyatkin\meta\Meta;
use infrajs\path\Path;
use infrajs\config\Config;
use infrajs\load\Load;
use infrajs\access\Access;

$meta = new Meta();

$meta->addVariable('srcs', function () {
	$r = explode('.', $this->action);
	$type = $r[sizeof($r) - 1];
	$srcs = [];
	Config::run( function ($name, $conf) use (&$srcs, $type){
		if (empty($conf[$type])) return;
		$list = $conf[$type];
		if (!is_array($list)) $list = [$list];
		foreach ($list as $s) {
			$src = Path::theme('-'.$name.'/'.$s);
			if (!$src) continue;
			if (Path::getExt($src) == 'php') $src = '-'.$name.'/'.$s;
			$srcs[] = $src;
		}
	});
	return $srcs;
});


$meta->addAction('all.js', function () {
	extract($this->gets(['srcs']), EXTR_REFS);
	if (!Load::isphp()) header('Content-Type: application/javascript; charset=utf-8');
	$html = '';
	foreach ($srcs as $src) $html .= "import { } from '/".$src."'\n";
	echo $html;
});
$meta->addAction('all.css', function () {
	extract($this->gets(['srcs']), EXTR_REFS);

	if (!Load::isphp()) header("Content-Type: text/css; charset=utf-8");
	$html = '';
	foreach ($srcs as $src) {
		if (Config::get('collect')['imports']) {
			$t = Access::updateTime();
			if (sizeof(explode('?',$src)) > 1) {
				$src = $src.'&t='.$t;
			} else {
				$src = $src.'?t='.$t;
			}
			$html .= "@import url('/$src');\r\n";
		} else {
			$html .= "\n\n".'/*load css '.$src."*/\r\n";
			$src = Path::theme($src);
			if (!$src) continue;
			$css = Load::loadTEXT($src);
			if (!$css) continue;	
			$html .= $css;
		}
	}
	echo $html;
});


$meta->init([
	'name'=>'collect'
]);