<?php

namespace Imagette\Converters;

use Nette\NotSupportedException;
use Nette\Utils\Image;

/**
 * Converts images to AVIF format.
 * @see https://en.wikipedia.org/wiki/AVIF
 */
class AvifConverter extends Converter {
	public function __construct() {
		if(PHP_VERSION_ID < 80100) {
			throw new NotSupportedException("AVIF can be used from PHP 8.1!");
		}
		
		$this->extension = "avif";
		$this->quality = -1;
		$this->type = Image::AVIF;
	}
}
