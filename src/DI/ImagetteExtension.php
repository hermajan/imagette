<?php

namespace Imagette\DI;

use Imagette\Thumbnails\Parameters\{Template, Thumbnails};
use Imagette\Thumbnails\Thumbnail;
use Imagette\Thumbnails\ThumbnailMacro;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\{Expect, Schema};

class ImagetteExtension extends CompilerExtension {
	public function getConfigSchema(): Schema {
		return Expect::structure([
			"thumbnails" => Expect::from(new Thumbnails, [
				"templates" => Expect::arrayOf(Expect::from(new Template)->castTo("array"))
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
