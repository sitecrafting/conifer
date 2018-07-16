<?php

/* @codingStandardsIgnoreFile */
namespace Conifer;

trait SupportsMetaSearch {

  /**
   * list_searcheable_acf list all the custom fields we want to include in our search query
   *
   * @return array list of custom fields
   */
  public static function list_searcheable_acf(){

    /*
     * If you just pass a string, it will do an equals comparison on that value
     * If you wish to do a like search operation (to search repearter rows for
     * Instance) pass an array with two values, the meta_key value, and
     * The meta_compare value
     * For example:
     *     ['meta_key' => "page_content_rows___accordion_sections___%", 'meta_compare'=>'LIKE']
     * This would search all accordion sections of an accordion repeater that is part of a page content repeater (make sense?)
     *
     * Be aware:
     * 1. ACF fields have two values in the database so when building likes make sure to filter out the field that starts with the underscore
     * 2. To search for repeater fields use the underscore which is a wildcard and can be used to grab repearter rows
     *
     */

    $list_searcheable_acf = [
      "test_field",
    ];

    return $list_searcheable_acf;
  }

  /**
  * advanced_custom_search search that encompasses ACF/advanced custom fields and taxonomies and split expression before request
   *
  * @param  query-part/string      $where    the initial "where" part of the search query
  * @param  object                 $wp_query
  * @return query-part/string      $where    the "where" part of the search query as we customized
  * see https://vzurczak.wordpress.com/2013/06/15/extend-the-default-wordpress-search/
  * credits to Vincent Zurczak for the base query structure/splitting tags section
  */
  public static function advanced_custom_search( $where, $wp_query ) {  //TODO: Revise the structure to make this more configurable
    global $wpdb;

    if ( empty( $where )) {
      return $where;
    }

    // Get the search term
    $searchString = $wp_query->query_vars[ 's' ];

    // Create an array of each word in the search string
    $arrSearchTerms = explode( ' ', $searchString );

    //Check if there is just one word in the search string
    if( $arrSearchTerms === FALSE || count( $arrSearchTerms ) == 0 ){
      $arrSearchTerms = array( 0 => $searchString );
    }

    // Reset the where clause
    $where = '';

    // Get searchable acf fields we want to query against
    $list_searcheable_acf = static::list_searcheable_acf();

    //For each search term build the query clause to look in the acf fields
    foreach( $arrSearchTerms as $tag ) {
      $where .= static::build_where_clause($list_searcheable_acf, $tag);
    }

    return $where;
  }

  /**
   * build_where_clause build the where string to include in our search query
   *
   * @param  array      $list_searcheable_acf       the list of searchable acf fields
   * @param  string     $tag                        the search value
   * @return string     $where                      where clause
   */
  public static function build_where_clause($list_searcheable_acf, $tag){
    global $wpdb;

    $where="";

    //Prepare search term
    $searchTerm="%".$tag."%";

    //Build the where clause
    $where.=$wpdb->prepare(
            " AND (
                ($wpdb->post_title LIKE %s)
                OR ($wpdb->post_content LIKE %s)
                OR EXISTS (
                    SELECT * FROM $wpdb->postmeta
                    WHERE post_id = $wpdb->ID AND (",
            $searchTerm,
            $searchTerm);

    //Add the search filters for each acf field
    $where .= static::build_meta_filters($searchTerm, $list_searcheable_acf);

    //Check comments and taxonmy tags
    $where .= $wpdb->prepare(")
        )
        OR EXISTS (
          SELECT * FROM $wpdb->comments
          WHERE comment_post_ID = $wpdb->ID
            AND comment_content LIKE %s
        )
        OR EXISTS (
          SELECT * FROM $wpdb->terms
          INNER JOIN $wpdb->term_taxonomy
            ON $wpdb->term_id= $wpdb->term_id
          INNER JOIN $wpdb->term_relationships
            ON $wpdb->term_taxonomy_id = $wpdb->term_taxonomy_id
          WHERE (
                    taxonomy = 'post_tag'
                    OR taxonomy = 'category'
                    )
            AND object_id = $wpdb->ID
            AND $wpdb->name LIKE %s
        )
    )", $searchTerm, $searchTerm);

    return $where;
  }

  /**
   * Build the WHERE clause for each ACF field to include in our search query
   *
   * @param  string     $searchTerm             the search value
   * @param  array      $list_searcheable_acf   the list of searchable acf fields
   * @return string     $where                  part of the where clause
   */
  public static function build_meta_filters($searchTerm, $list_searcheable_acf){
    global $wpdb;
    $where="";
    foreach ($list_searcheable_acf as $index=>$searcheable_acf) {

      //Set the value of the meta key, this is the acf field we want to search in.
      if (isset($searcheable_acf['meta_key'])){
        $meta_key = $searcheable_acf['meta_key'];
      } else {
        $meta_key = $searcheable_acf;
      }

      // Set the value of the compare if it is set, or set it to '=' if none is set
      if (isset($searcheable_acf['meta_compare'])){
        $meta_compare = $searcheable_acf['meta_compare'];
      } else {
        $meta_compare = '=';
      }

      // Build the filter strings
      if ($index==0){
        $where .= $wpdb->prepare(" (meta_key ".$meta_compare." %s AND meta_value LIKE %s) ", $meta_key, $searchTerm);
      } else {
        $where .= $wpdb->prepare(" OR (meta_key ".$meta_compare." %s AND meta_value LIKE %s) ", $meta_key, $searchTerm);
      }
    }

    return $where;
  }
}
