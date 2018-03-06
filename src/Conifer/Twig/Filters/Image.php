<?php
/**
 * Custom Twig filters for dealing with images
 */

namespace Conifer\Twig\Filters;

/**
 * Twig Wrapper around high-level image filters
 *
 * @package Conifer
 */
class Image extends AbstractBase {
	/**
	 * Get the Twig functions to register
	 * @return  array an associative array of callback functions, keyed by name
	 */
	public function get_filters() {
		return [
			'src_to_retina' => function( $src ) {
				return preg_replace('~(\.[a-z]+)$~i', '@2x$1', $src);
			},
		];
	}
}
