<?php

namespace Imagette\Thumbnails;

use Imagette\Converters\{AvifConverter, WebpConverter};
use Imagette\Picture;
use Nette\DirectoryNotFoundException;
use Nette\StaticClass;
use Nette\Utils\{Finder, Image, ImageException, UnknownImageFileException};

class Thumbnail {
	use StaticClass;
	
	private static array $parameters = [];
	
	public function __construct(array $parameters) {
		self::$parameters = $parameters;
	}
	
	public static function getParameters(): array {
		return self::$parameters;
	}
	
	public static function getPath(string $path): string {
		$template = self::$parameters["templates"];
		if(isset($template[$path])) {
			return $template[$path]["path"];
		} else {
			return $path;
		}
	}
	
	public static function isValid(string $path, string $file): bool {
		$filename = self::$parameters["base"].$path.$file;
		return Picture::isValid($filename);
	}
	
	/**
	 * Cleans thumbnails
	 */
	public static function clean(): array {
		$result = [];
		$files = Finder::findFiles("*")->in(self::$parameters["folder"]);
		foreach($files as $file) {
			if(unlink($file->getPathname())) {
				$result[] = $file->getPathname();
			}
		}
		return $result;
	}
	
	/**
	 * Returns the path and filename of image
	 */
	public static function getSource(string $path, string $filename, ?int $width = null, ?int $height = null, array $flags = [], ?int $quality = null, array $formats = []): string {
		if(!file_exists(self::$parameters["folder"]) and !mkdir(self::$parameters["folder"], 0755)) {
			throw new DirectoryNotFoundException("Path `".self::$parameters["folder"]."` does not exist!");
		}
		
		if(array_key_exists($path, self::$parameters["templates"])) {
			$template = $path;
			$path = self::$parameters["templates"][$template]["path"];
			$width = self::$parameters["templates"][$template]["width"] ?? null;
			$height = self::$parameters["templates"][$template]["height"] ?? null;
			$flags = self::$parameters["templates"][$template]["flags"] ?? [];
			$quality = self::$parameters["templates"][$template]["quality"] ?? null;
		}
		
		if(empty($flags)) {
			$flags = self::$parameters["flags"];
		}
		
		if(empty($formats)) {
			$formats = self::$parameters["formats"];
		}
		
		$destination = self::resize($path, $filename, $width, $height, $flags, $quality, $formats);
		return substr($destination, strlen(realpath(self::$parameters["base"])));
	}
	
	/**
	 * Resizes image
	 */
	private static function resize(string $path, string $file, ?int $width = null, ?int $height = null, array $flags = [], ?int $quality = null, array $formats = []): string {
		if(!self::isValid($path, $file)) {
			return self::$parameters["fallback"];
		}
		
		$filename = self::$parameters["base"].$path.$file;
		if(file_exists($filename) and !isset($width) and !isset($height)) {
			return $filename;
		}
		
		$resizeFlags = 0;
		foreach($flags as $flag) {
			if(is_string($flag)) {
				$resizeFlags |= constant($flag);
			} elseif(is_int($flag)) {
				$resizeFlags |= $flag;
			}
		}
		
		$folder = str_replace(DIRECTORY_SEPARATOR, "-", $path);
		$mask = $width."x".$height."f".$resizeFlags."q".$quality;
		$destination = self::$parameters["folder"].pathinfo($filename, PATHINFO_FILENAME)."_".$folder.$mask.filemtime($filename).".".pathinfo($filename, PATHINFO_EXTENSION);
		
		if(!file_exists($destination) and file_exists($filename)) {
			try {
				$image = Image::fromFile($filename);
				if($width or $height) {
					$image->resize($width, $height, $resizeFlags);
				}
				
				$image->save($destination, $quality);
			} catch(UnknownImageFileException|ImageException $e) {
				return self::$parameters["fallback"];
			}
		}
		
		self::convert($destination, $formats);
		return $destination;
	}
	
	protected static function convert(string $path, array $formats = []) {
		foreach($formats as $format) {
			if(is_string($format)) {
				$type = constant($format);
			} else {
				$type = $format;
			}
			
			switch($type) {
				case Image::AVIF:
					$avifConverter = new AvifConverter();
					$avifConverter->convert($path);
					break;
				case Image::WEBP:
					$webpConverter = new WebpConverter();
					$webpConverter->convert($path);
					break;
			}
		}
	}
}
