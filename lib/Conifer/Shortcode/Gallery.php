<?php
/**
 * Custom gallery functionality
 */

namespace Conifer\Shortcode;

use Timber\Timber;

use Conifer\Post\Image;
use Conifer\Post\Post;

/**
 * Implements a custom gallery shortcode that overrides the native shortcode.
 *
 * @package Conifer
 */
class Gallery extends AbstractBase {
  /**
   * Render the gallery
   */
  public function render( $atts = [] ) {
    $data = [];

    if( $atts['ids'] ) {
      // Get an array of IDs
      $ids = explode( ',', $atts['ids'] );

      $data['gallery_images'] = $this->get_images( $ids );
    }

    return Timber::compile( 'shortcodes/gallery.twig', $data );
  }

  /**
   * Get an array of TimberImages by the given IDs
   *
   * @param  array $ids the attachment post IDs to fetch
   * @return array      a numeric array of TimberImage objects
   */
  protected function get_images( $ids ) {
    // Make sure we only query for images by valid, real IDs
    $ids = array_filter( $ids, function($id) {
      return filter_var( $id, FILTER_VALIDATE_INT ) && Post::exists( $id );
    });

    // Get a TimberImage for each valid ID
    return array_map( function( $id ) {
      return new Image($id);
    }, $ids );
  }
}
