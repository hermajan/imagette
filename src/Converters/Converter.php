<?php
namespace Imagette\Converters;

use Nette\Utils\{Finder, Image};

abstract class Converter {
	abstract public function convert(string $path, bool $replace = false);
	
	public function convertFolder(string $path, bool $replace = false, array $masks = ["*"]) {
		$finder = Finder::findFiles($masks)->from($path);
		/** @var \SplFileInfo $file */
		foreach($finder as $key => $file) {
			if($file->getExtension() != "webp") {
//				echo $key.PHP_EOL;
				$this->convert($key, $replace);
			}
		}
	}
	
	public function save(Image $image, string $file, string $suffix = "", bool $replace = false) {
		$path = pathinfo($file, PATHINFO_DIRNAME)."/".pathinfo($file, PATHINFO_FILENAME);
		$filename = $path.$suffix.".".pathinfo($file, PATHINFO_EXTENSION);
		
		if($replace === true) {
			$image->save($filename);
		} else {
			if(!file_exists($filename)) {
				$image->save($filename);
			}
		}
		
		return $image;
	}
	
	public static function isValid(string $filename): bool {
		$exists = file_exists($filename) and is_file($filename);
		if($exists === true) {
			try {
				Image::fromFile($filename);
			} catch(\Exception $e) {
				return false;
			}
		} else {
			return false;
		}
		
		return true;
	}
}
