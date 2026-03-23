<?php

namespace Imagette\Thumbnails;

use Latte\Macros\MacroSet;

/**
 * Latte macro for creating thumbnails.
 */
class ThumbnailMacro extends MacroSet {
	/**
	 * Installs macro between Latte macros.
	 */
	public static function install($compiler) {
		$lc = new self($compiler);
		$lc->addMacro("thumbnail", function($node, $writer) {
			return $writer->write("echo ".Thumbnail::class."::create(%node.word, %node.args)");
		});
		return $lc;
	}
}
