<?php

namespace Imagette\Latte;

use Latte\{CompileException, Compiler, MacroNode, Macros\MacroSet, PhpWriter};
use Imagette\Thumbnails\Thumbnail;

/**
 * Latte macro for creating thumbnails.
 */
class ThumbnailMacro extends MacroSet {
	/**
	 * Installs macro between Latte macros.
	 */
	public static function install(Compiler $compiler): ThumbnailMacro {
		$lc = new self($compiler);
		$lc->addMacro("thumbnail", function(MacroNode $node, PhpWriter $writer) {
			return $writer->write("echo ".Thumbnail::class."::getSource(%node.word, %node.args)");
		});
		return $lc;
	}
}
