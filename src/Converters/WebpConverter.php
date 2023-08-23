<?php
namespace Imagette\Converters;

use Nette\Utils\{Image, ImageException, UnknownImageFileException};

class WebpConverter extends Converter {
	private string $extension = "webp";
	private int $quality = 30;
	private int $type = Image::WEBP;
	
	/**
	 * @throws ImageException
	 * @throws UnknownImageFileException
	 */
	public function convert(string $path, bool $replace = false, bool $echo = false) {
		$filename = pathinfo($path, PATHINFO_DIRNAME)."/".pathinfo($path, PATHINFO_FILENAME);
		
		if($this::isValid($path)) {
			$image = Image::fromFile($path);
			if($replace === true) {
				$image->save($filename.".".$this->extension, $this->quality, $this->type);
				
				if($echo === true) {
					echo $path." -> ".$this->extension.PHP_EOL;
				}
			} else {
				if(!file_exists($filename.".".$this->extension)) {
					$image->save($filename.".".$this->extension, $this->quality, $this->type);
					
					if($echo === true) {
						echo $path." -> ".$this->extension.PHP_EOL;
					}
				} else {
					if($echo === true) {
						echo "File ".$path." not exists!".PHP_EOL;
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
