<?php
namespace infrajs\collect;
use infrajs\load\Load;
use infrajs\config\Config;
use infrajs\each\Each;
use infrajs\path\Path;

class Collect {
	public static function js()
	{
		header('Infrajs-Cache: false');
		$js = 'window.infra={}; window.infrajs={ }; infra.conf=('.Load::json_encode(Config::pub()).'); infra.config=function(){ return infra.conf; };';
		$conf = Config::get();
		foreach($conf as $name=>$c){
			Collect::loadJS($js, $name);	
		}
		return $js;
	}
	public static $collected=array();
	public static function loadJS(&$js, $name)
	{
		$c = Config::get($name);
		if (empty($c['js'])) return;
		if (!empty($c['off'])) return;
		if (!empty($c['dependencies'])) {
			Each::exec($c['dependencies'], function($name) use(&$js){
				Collect::loadJS($js, $name);
			});
		}
		if (Collect::$collected[$name]) return;
		Collect::$collected[$name] = true;

		Each::exec($c['js'], function ($path) use ($name,&$js) {
			$src = '-'.$name.'/'.$path;
			if(!Path::theme($src)) {
				echo '<pre>';
				throw new \Exception('Не найден файл '.$src);
			}
			$js.= "\n\n".'//require js '.$src."\r\n";
			$js.= Load::loadTEXT($src).';';
		});
	}
}