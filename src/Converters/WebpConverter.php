<?php

namespace Imagette\Converters;

use Nette\Utils\Image;
use WebPConvert\Convert\Converters\Stack;
use WebPConvert\Convert\Exceptions\ConversionFailedException;

/**
 * Converts images to WebP format.
 * @see https://en.wikipedia.org/wiki/WebP
 */
class WebpConverter extends Converter {
	public function __construct() {
		$this->extension = "webp";
		$this->quality = -1;
		$this->type = Image::WEBP;
	}
	
	public function convert(string $path, bool $replace = false): bool {
		try {
			$options = [
				"converters" => ["cwebp", "vips", "imagick", "gmagick", "imagemagick", "graphicsmagick", "gd"],
				"metadata" => "all"
			];
			if($this->quality !== -1) {
				$options["quality"] = $this->quality;
			}
			
			Stack::convert($path, $path.".".$this->extension, $options);
			return true;
		} catch(ConversionFailedException $e) {
			return parent::convert($path, $replace);
		}
	}
}
