<?php

namespace Imagette\Converters;

use Nette\Utils\{Finder, Image, ImageException, UnknownImageFileException};

abstract class Converter {
	protected string $extension = "";
	
	protected int $quality = -1;
	
	protected int $type;
	
	/**
	 * Converts image to some format.
	 * @param string $path
	 * @param bool $replace
	 * @return bool True if image is converted, false otherwise.
	 */
	public function convert(string $path, bool $replace = false): bool {
		if(file_exists($path)) {
			if($this->extension !== pathinfo($path, PATHINFO_EXTENSION)) {
				try {
					if($replace === true) {
						$this->save($path);
					} else {
						if(!file_exists($path.".".$this->extension)) {
							$this->save($path);
						} else {
							return false;
						}
					}
				} catch(\Exception $e) {
					return false;
				}
			} else {
				return false;
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Converts folder with images to some format.
	 * @param string $path Path to file.
	 * @param bool $replace True if converted file should be replaced, false otherwise.
	 * @param array $masks Masks where search for files. See <https://doc.nette.org/en/utils/finder#toc-wildcards>.
	 * @throws ImageException
	 */
	public function convertFolder(string $path, bool $replace = false, array $masks = ["*"]): void {
		$finder = Finder::findFiles($masks)->from($path);
		/** @var \SplFileInfo $file */
		foreach($finder as $key => $file) {
			$this->convert($key, $replace);
		}
	}
	
	/**
	 * @throws ImageException
	 * @throws UnknownImageFileException
	 */
	public function save(string $filename) {
		$image = Image::fromFile($filename);
		$image->paletteToTrueColor();
		$image->save($filename.".".$this->extension, $this->quality, $this->type);
	}
}
