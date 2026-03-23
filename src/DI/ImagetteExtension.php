<?php

namespace Imagette\DI;

use Imagette\Thumbnails\Thumbnail;
use Imagette\Thumbnails\ThumbnailMacro;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;

class ImagetteExtension extends CompilerExtension {
	public $defaults = [
		"thumbnails" => [
			"base" => null,
			"folder" => null,
			"fallback" => null,
			"flags" => ["Nette\Utils\Image::SHRINK_ONLY"],
			"formats" => [],
			"templates" => [],
		]
	];

	public function loadConfiguration() {
		$config = $this->validateConfig($this->defaults);
		$builder = $this->getContainerBuilder();
		
		$builder->addDefinition($this->prefix("thumbnails"))
			->setFactory(Thumbnail::class, [$config["thumbnails"]]);
		
		$latteFactory = $builder->getDefinition("latte.latteFactory");
		$latteFactory->addSetup(ThumbnailMacro::class."::install(?->getCompiler())", ["@self"]);
	}
	
	public function afterCompile(ClassType $class) {
		$initialize = $class->getMethod("initialize");
		$initialize->addBody('$this->getService(?);', [$this->prefix("thumbnails")]);
	}
}
