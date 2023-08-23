<?php
namespace Imagette\Converters;

use Nette\Utils\{Image};

class AvifConverter extends Converter {
	const EXTENSION = "avif";
	
	public function convert(string $path, bool $replace = false, bool $echo = false) {
		$filename = pathinfo($path, PATHINFO_DIRNAME)."/".pathinfo($path, PATHINFO_FILENAME);
		
		if($this::isValid($path)) {
			$image = Image::fromFile($path);
			if($replace === true) {
				$image->save($filename.".".self::EXTENSION, 30, Image::AVIF);
				
				if($echo === true) {
					echo $path." -> avif".PHP_EOL;
				}
			} else {
				if(!file_exists($filename.".".self::EXTENSION)) {
					$image->save($filename.".".self::EXTENSION, 30, Image::AVIF);
					
					if($echo === true) {
						echo $path." -> avif".PHP_EOL;
					}
				} else {
					if($echo === true) {
						echo "File ".$path." not converted!".PHP_EOL;
					}
				}
			}
		} else {
			if($echo === true) {
				echo "File ".$path." is not valid!".PHP_EOL;
			}
		}
	}
}
