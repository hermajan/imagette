<?php

namespace Imagette\Thumbnails;

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
	public static function getSource(string $path, string $filename, ?int $width = null, ?int $height = null, array $flags = [], ?int $quality = null, ?string $type = null): string {
		if(!file_exists(self::$parameters["folder"])) {
			if(!mkdir(self::$parameters["folder"])) {
				throw new DirectoryNotFoundException("Path `".self::$parameters["folder"]."` does not exist!");
			}
		}
		
		if(isset(self::$parameters["templates"][$path])) {
			$path = self::$parameters["templates"][$path]["path"];
			$width = self::$parameters["templates"][$path]["width"] ?? null;
			$height = self::$parameters["templates"][$path]["height"] ?? null;
			$flags = self::$parameters["templates"][$path]["flags"] ?? [];
			$quality = self::$parameters["templates"][$path]["quality"] ?? null;
		}
		
		$destination = self::resize($path, $filename, $width, $height, $flags, $quality);
		return substr($destination, strlen(realpath(self::$parameters["base"])));
	}
	
	/**
	 * Resizes image
	 */
	private static function resize(string $path, string $file, ?int $width = null, ?int $height = null, array $flags = [], ?int $quality = null): string {
		$filename = self::$parameters["base"].$path.$file;
		if(!is_file($filename) or !file_exists($filename)) {
			return self::$parameters["fallback"];
		}
		
		if(file_exists($filename) and !$width and !$height) {
			return $filename;
		}
		
		$resizeFlags = 0;
		Debugger::barDump([$resizeFlags, $flags]);
		if(empty($flags)) {
			$flags = self::$parameters["flags"];
		}
//		else {
			foreach($flags as $flag) {
				if(is_string($flag)) {
					$resizeFlags |= constant($flag);
				} elseif(is_int($flag)) {
					$resizeFlags |= $flag;
				}
			}
//		}
		Debugger::barDump($resizeFlags);
		
		$folder = str_replace(DIRECTORY_SEPARATOR, "-", $path);
		$mask = $width."x".$height."f".$resizeFlags."q".$quality;
		$destination = self::$parameters["folder"].pathinfo($filename, PATHINFO_FILENAME)."_".$folder.$mask.filemtime($filename).".".pathinfo($filename, PATHINFO_EXTENSION);
		Debugger::barDump($destination);
		
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
		return $destination;
	}
}
