<?php
namespace Imagette;

use Nette\Utils\Image;

class Picture {
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
