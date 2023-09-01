<?php

namespace Imagette\Thumbnails;

use Latte\{Compiler, MacroNode, Macros\MacroSet, PhpWriter};

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
			return $writer->write("echo ".Thumbnail::class."::create(%node.word, %node.args)");
		});
		return $lc;
	}
}
