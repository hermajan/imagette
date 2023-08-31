<?php

namespace Imagette\DI;

use Imagette\Latte\ThumbnailMacro;
use Imagette\Thumbnails\Thumbnail;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\{Expect, Schema};
use Nette\Utils\Image;

class ImagetteExtension extends CompilerExtension {
	public function getConfigSchema(): Schema {
		return Expect::structure([
			"thumbnails" => Expect::structure([
				"base" => Expect::string(),
				"folder" => Expect::string()->required(),
				"fallback" => Expect::string(),
				"flags" => Expect::arrayOf(Expect::anyOf(Expect::string(), Expect::int()))->default([Image::SHRINK_ONLY]),
				"templates" => Expect::arrayOf(Expect::structure([
					"path" => Expect::string()->required(),
					"width" => Expect::int()->nullable(),
					"height" => Expect::int()->nullable(),
					"flags" => Expect::arrayOf(Expect::anyOf(Expect::string(), Expect::int()))->default([Image::SHRINK_ONLY]),
					"quality" => Expect::int()->nullable()
				])->castTo("array"))
			])->castTo("array")
		]);
	}
	
	public function loadConfiguration(): void {
		$builder = $this->getContainerBuilder();
		
		$builder->addDefinition($this->prefix("thumbnails"))
			->setFactory(Thumbnail::class)->setArguments([$this->config->thumbnails]);
		
		$latteFactory = $builder->getDefinition("latte.latteFactory");
		$latteFactory->getResultDefinition()->addSetup(ThumbnailMacro::class."::install(?->getCompiler())", ["@self"]);
	}
	
	public function afterCompile(ClassType $class) {
		$this->initialization->addBody('$this->getService(?);', [$this->prefix("thumbnails")]);
	}
}
