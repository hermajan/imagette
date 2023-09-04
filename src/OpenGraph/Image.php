<?php

namespace Imagette\OpenGraph;

use Nette\Utils\{Image as NetteImage, ImageException, UnknownImageFileException};

class Image {
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
