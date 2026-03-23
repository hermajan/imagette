<?php

namespace Imagette\Thumbnails\Parameters;

class Template {
	/**
	 * Path of folder with images
	 * @var string
	 */
	public $path;
	
	/**
	 * Width of thumbnail
	 * @var int|null
	 */
	public $width = null;
	
	/**
	 * Height of thumbnail
	 * @var int|null
	 */
	public $height = null;
	
	/**
	 * Flags for resizing
	 * @var array
	 */
	public $flags = [];
	
	/**
	 * Quality of thumbnail
	 * @var int|null
	 */
	public $quality = null;
}
