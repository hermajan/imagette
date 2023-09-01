<?php

namespace Imagette\Thumbnails\Parameters;

use Nette\Utils\Image;

class Thumbnails {
	/** Base folder with images*/
	public string $base;
	
	/** Path to folder with thumbnails */
	public string $folder;
	
	/** Filename of fallback thumbnail image */
	public string $fallback;
	
	/** Flags for resizing */
	public array $flags = [Image::SHRINK_ONLY];
	
	/** Formats to which thumbnail also convert */
	public array $formats = [];
}
