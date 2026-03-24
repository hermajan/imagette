<?php

namespace Imagette\OpenGraph;

use Nette\Utils\{Image as NetteImage, ImageException, UnknownImageFileException};

class Image {
	/**
	 * Converts an image to a specific format or size and saves it to the destination.
	 * If the source file does not exist, the fallback image is used.
	 *
	 * @param string $source The file path of the source image.
	 * @param string $destination The file path where the converted image will be saved.
	 * @param string $fallback The file path of the fallback image if the source is not available or an error occurs.
	 * @return string Returns the file path of the saved image, or the fallback path if an error occurs.
	 */
	public function convert(string $source, string $destination, string $fallback): string {
		if(!file_exists($destination)) {
			try {
				if(file_exists($source)) {
					$image = NetteImage::fromFile($source);
				} else {
					$image = NetteImage::fromFile($fallback);
				}
			} catch(UnknownImageFileException $e) {
				return $fallback;
			}
			
			if($image->getWidth() > $image->getHeight()) {
				$size = $image->getWidth();
				
				// make rectangle
//                    $picture = Image::fromBlank($size, $size / 2, Image::rgb(255, 255, 255));
				
				$picture = $image->resize($size, (int)($size / 1.91), NetteImage::EXACT);
			} else {
				$size = $image->getHeight();
				
				// make rectangle
				$picture = NetteImage::fromBlank($size * 1.91, $size, NetteImage::rgb(255, 255, 255));
			}
			
			$picture->place($image, "50%", "50%");
			try {
				$picture->save($destination);
			} catch(ImageException $e) {
				return $fallback;
			}
		}
		
		return $destination;
	}
}
