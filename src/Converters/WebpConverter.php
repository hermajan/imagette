<?php

namespace Imagette\Converters;

use Nette\Utils\Image;

/**
 * Converts images to WebP format.
 * @see https://en.wikipedia.org/wiki/WebP
 */
class WebpConverter extends Converter {
	public function __construct() {
		$this->extension = "webp";
		$this->quality = 30;
		$this->type = Image::WEBP;
	}
}
