<?php
namespace Imagette;

use Nette\Utils\Image;

class Picture {
	public static function exists(string $filename): bool {
		return file_exists($filename) and is_file($filename);
	}
	
	public static function isValid(string $filename): bool {
		$exists = Picture::exists($filename);
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
