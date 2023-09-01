<?php

namespace Imagette\Thumbnails;

use Imagette\Converters\{AvifConverter, WebpConverter};
use Imagette\Picture;
use Nette\DirectoryNotFoundException;
use Nette\StaticClass;
use Nette\Utils\{Finder, Image, ImageException, UnknownImageFileException};
use Tracy\Debugger;

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
	
	/**
	 * Cleans thumbnails
	 * @return array Array of removed thumbnails filenames
	 */
	public static function clean(): array {
		$result = [];
		$files = Finder::findFiles("*")->in(self::$parameters["folder"]);
		/** @var \SplFileInfo $file */
		foreach($files as $file) {
			if(unlink($file->getPathname())) {
				$result[] = $file->getPathname();
			}
		}
		return $result;
	}
	
	public static function generate() {
		$templates = self::$parameters["templates"];
		foreach($templates as $key => $template) {
			if(array_key_exists("path", $template)) {
				$path = self::$parameters["base"].$template["path"];
				$i = 0;
				$files = Finder::findFiles("*")->in($path);
				/** @var \SplFileInfo $file */
				foreach($files as $file) {
					$r = self::create($key, $file->getFilename(), $template["width"] ?? null, $template["height"] ?? null, $template["flags"] ?? [], $template["quality"] ?? null, self::$parameters["formats"] ?? []);
					Debugger::dump($r);
					
					if($i > 5) {
						break;
					}
					$i++;
				}
			}
			break;
		}
	}
	
	/**
	 * Returns the path and filename of thumbnail of image
	 */
	public static function create(string $path, string $filename, ?int $width = null, ?int $height = null, array $flags = [], ?int $quality = null, array $formats = []): string {
		if(!file_exists(self::$parameters["folder"]) and !mkdir(self::$parameters["folder"], 0755)) {
			throw new DirectoryNotFoundException("Path `".self::$parameters["folder"]."` does not exist!");
		}
		
		$template = $path;
		if(array_key_exists($template, self::$parameters["templates"])) {
			$path = self::$parameters["base"].self::$parameters["templates"][$template]["path"];
			$width = self::$parameters["templates"][$template]["width"] ?? null;
			$height = self::$parameters["templates"][$template]["height"] ?? null;
			$flags = self::$parameters["templates"][$template]["flags"] ?? [];
			$quality = self::$parameters["templates"][$template]["quality"] ?? null;
		}
		
		if(empty($flags)) {
			$flags = self::$parameters["flags"];
		}
		$resizeFlags = 0;
		foreach($flags as $flag) {
			if(is_string($flag)) {
				$resizeFlags |= constant($flag);
			} elseif(is_int($flag)) {
				$resizeFlags |= $flag;
			}
		}
		
		$source = $path.$filename;
		$resized = self::resize($source, $template, $width, $height, $resizeFlags, $quality);
		
		if(empty($formats)) {
			$formats = self::$parameters["formats"];
		}
		self::convert($resized, $formats);
		
		return substr($resized, strlen(realpath(self::$parameters["base"])));
	}
	
	/**
	 * Resizes image into thumbnail
	 */
	protected static function resize(string $filename, string $template = "", ?int $width = null, ?int $height = null, int $flags = Image::FIT, ?int $quality = null): string {
		if(file_exists($filename) and !isset($width) and !isset($height)) {
			return $filename;
		}
		
		if(!Picture::isValid($filename)) {
			return self::$parameters["fallback"];
		}
		
		$temp = "_".str_replace(DIRECTORY_SEPARATOR, "-", $template)."_";
		$mask = $width."x".$height."f".$flags."q".$quality;
		$mtime = "mt".filemtime($filename);
		$destination = self::$parameters["folder"].pathinfo($filename, PATHINFO_FILENAME).$temp.$mask.$mtime.".".pathinfo($filename, PATHINFO_EXTENSION);
		
		if(file_exists($filename) and !file_exists($destination)) {
			try {
				$image = Image::fromFile($filename);
				if($width or $height) {
					$image->resize($width, $height, $flags);
				}
				$image->save($destination, $quality);
			} catch(UnknownImageFileException|ImageException $e) {
				return self::$parameters["fallback"];
			}
		}
		
		return $destination;
	}
	
	/**
	 * Converts thumbnail to other formats
	 * @param string $filename Absolute path of the file
	 * @param array $formats Formats to which convert
	 * @throws ImageException
	 */
	protected static function convert(string $filename, array $formats = []) {
		foreach($formats as $format) {
			if(is_string($format)) {
				$type = constant($format);
			} else {
				$type = $format;
			}
			
			switch($type) {
				case "avif":
				case Image::AVIF:
					$avifConverter = new AvifConverter();
					$avifConverter->convert($filename);
					break;
				case "webp":
				case Image::WEBP:
					$webpConverter = new WebpConverter();
					$webpConverter->convert($filename);
					break;
			}
		}
	}
}
