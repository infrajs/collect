<?php
namespace infrajs\collect;
use infrajs\load\Load;
use infrajs\config\Config;
use infrajs\each\Each;
use infrajs\once\Once;
use infrajs\path\Path;

class Collect {
	public static function js($name = false)
	{
		return Once::func( function ($name) {
			$js = '';
			if ($name) {
				$ar = explode(',', $name);
				for ($i = 0,$l = sizeof($ar); $i < $l; $i++) {
					$name = $ar[$i];
					Collect::loadJS($js, $name);
				}
			} else {
				$conf = Config::get();
				Collect::loadJS($js, 'load');
				foreach ($conf as $name => $c){
					Collect::loadJS($js, $name);	
				}
			}
			return $js;
		}, array($name));
	}
	public static function css($name = false)
	{
		return Once::func( function ($name) {
			$js = '';
			if ($name) {
				$ar = explode(',', $name);
				for ($i = 0,$l = sizeof($ar); $i < $l; $i++) {
					$name = $ar[$i];
					Collect::loadCSS($js, $name);
				}
			} else {
				$conf = Config::get();
				foreach ($conf as $name => $c){
					Collect::loadCSS($js, $name);	
				}
			}
			return $js;
		}, array($name));
	}
	public static function loadJS(&$js, $name)
	{
		return Once::func( function ($name) use (&$js) {
			$c = Config::get($name);
			if (!empty($c['off'])) return;
			if (!empty($c['dependencies'])) {
				Each::exec($c['dependencies'], function &($name) use(&$js){
					Collect::loadJS($js, $name);
					$r = null; return $r;
				});
			}
			
			$root = (!empty($c['-collect']) && $c['-collect'] == 'root');
			
			if (isset($c['json'])) {
				Each::exec($c['json'], function &($src) use ($name, &$js,  $root) {
					$r = null;
					if (!$root) $src = '-'.$name.'/'.$src;
					$js.= "\n\n".'//load json '.$src."\r\n";
					if (Path::theme($src)) {
						$js.= 'infra.store("loadJSON")["'.$src.'"] = { value: '.Load::loadTEXT($src).', status:true};';
					} else {
						$js.= 'console.error("Не найден файл '.$src.'");';
					}
					return $r;
				});
			}
			if (isset($c['js'])) {
				Each::exec($c['js'], function &($src) use ($name, &$js,  $root) {
					$r = null;
					if (!$root) $src = '-'.$name.'/'.$src;

					$js.= "\n\n".'//load js '.$src."\r\n";
					if (Path::theme($src)) {
						$js.= Load::loadTEXT($src).';';
					} else {
						$js.= 'console.error("Не найден файл '.$src.'");';
					}
					return $r;
				});
			}
		}, array($name));
	}
	public static $cssed = array();
	public static function loadCSS(&$js, $name)
	{
		$c = Config::get($name);
		if (empty($c['css'])) return;
		if (!empty($c['off'])) return;
		if (!empty($c['dependencies'])) {
			Each::exec($c['dependencies'], function &($name) use(&$js){
				Collect::loadCSS($js, $name);
				$r = null;
				return $r;
			});
		}
		if (!empty(Collect::$cssed[$name])) return;
		Collect::$cssed[$name] = true;
		
		$root = (!empty($c['-collect']) && $c['-collect'] == 'root');

		Each::exec($c['css'], function &($path) use ($name, &$js, $root) {
			$r = null;
			if ($root) {
				$src = $path;
			} else {
				$src = '-'.$name.'/'.$path;
			}

			$js.= "\n\n".'/*load css '.$src."*/\r\n";
			if(!Path::theme($src)) {
				echo '<pre>';
				throw new \Exception('Не найден файл '.$src);
			}
			$js.= Load::loadTEXT('-csspath/?src='.$src);
			return $r;
		});
	}
}