<?php

namespace Imagette\Thumbnails\Parameters;

use Nette\Utils\Image;

class Thumbnails {
	/**
	 * Base folder with images
	 * @var string
	 */
	public $base;
	
	/**
	 * Path to folder with thumbnails
	 * @var string
	 */
	public $folder;
	
	/**
	 * Filename of fallback thumbnail image
	 * @var string
	 */
	public $fallback;
	
	/**
	 * Flags for resizing
	 * @var array
	 */
	public $flags = [Image::SHRINK_ONLY];
	
	/**
	 * Formats to which thumbnail also convert
	 * @var array
	 */
	public $formats = [];
}
