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
	 * @throws ImageException
	 */
	public function convert(string $path, bool $replace = false): bool {
		$extension = pathinfo($path, PATHINFO_EXTENSION);
		if($extension !== $this->extension) {
			try {
				$image = Image::fromFile($path);
			} catch(UnknownImageFileException $e) {
				return false;
			}
			
			if($replace === true) {
				$this->save($image, $path);
			} else {
				if(!file_exists($path.".".$this->extension)) {
					$this->save($image, $path);
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
		
		return true;
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
	 */
	public function save(Image $image, string $filename) {
		$image->paletteToTrueColor();
		$image->save($filename.".".$this->extension, $this->quality, $this->type);
	}
}
