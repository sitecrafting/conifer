<?php

// TODO move to Conifer\Post
namespace Conifer;

class AcfSearch {

  /**
   * list_searcheable_acf list all the custom fields we want to include in our search query
   *
   * @return array list of custom fields
   */
  public static function list_searcheable_acf(){

    /*
     * IF YOU JUST PASS A STRING, IT WILL DO AN EQUALS COMPARISON ON THAT VALUE
     * IF YOU WISH TO DO A LIKE SEARCH OPERATION (TO SEARCH REPEARTER ROWS FOR
     * INSTANCE) PASS AN ARRAY WITH TWO VALUES, THE meta_key VALUE, AND
     * THE meta_compare VALUE
     * FOR EXAMPLE:
     *     ['meta_key' => "page_content_rows___accordion_sections___%", 'meta_compare'=>'LIKE']
     * THIS WOULD SEARCH ALL ACCORDION SECTIONS OF AN ACCORDION REPEATER THAT IS PART OF A PAGE CONTENT REPEATER (make sense?)
     *
     * BE AWARE:
     * 1. ACF FIELDS HAVE TWO VALUES IN THE DATABASE SO WHEN BUILDING LIKES MAKE SURE TO FILTER OUT THE FIELD THAT STARTS WITH THE UNDERSCORE
     * 2. TO SEARCH FOR REPEATER FIEDLS USE THE UNDERSCORE WHICH IS A WILDCARD AND CAN BE USED TO GRAB REPEARTER ROWS
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
  * credits to Vincent Zurczak for the base query structure/spliting tags section
  */
  public static function advanced_custom_search( $where, $wp_query ) {
    global $wpdb;

    if ( empty( $where )) {
      return $where;
    }

    // GET THE SEARCH TERM
    $searchString = $wp_query->query_vars[ 's' ];

    // CREATE AN ARRAY OF EACH WORD IN THE SEARCH STRING
    $arrSearchTerms = explode( ' ', $searchString );

    //CHECK IF THERE IS JUST ONE WORD IN THE SEARCH STRING
    if( $arrSearchTerms === FALSE || count( $arrSearchTerms ) == 0 ){
      $arrSearchTerms = array( 0 => $searchString );
    }

    // RESET THE WHERE CLAUSE
    $where = '';

    // GET SEARCHABLE ACF FIELDS WE WANT TO QUERY AGAINST
    $list_searcheable_acf = static::list_searcheable_acf();

    //FOR EACH SEARCH TERM BUILD THE QUERY CLAUSE TO LOOK IN THE ACF FIELDS
    foreach( $arrSearchTerms as $tag ) {
      $where .= static::buildWhereClause($list_searcheable_acf, $tag);
    }

    return $where;
  }

  /**
   * buildWhereClause build the where string to include in our search query
   *
   * @param  array      $list_searcheable_acf       the list of searchable acf fields
   * @param  string     $tag                        the search value
   * @return string     $where                      where clause
   */
  public static function buildWhereClause($list_searcheable_acf, $tag){
    global $wpdb;

    $table_prefix = $wpdb->prefix;

    $where="";

    //PREPARE SEARCH TERM
    $searchTerm="%".$tag."%";

    //BUILD THE WHERE CLAUSE
    $where.=$wpdb->prepare(
            " AND (
                (".$table_prefix."posts.post_title LIKE %s)
                OR (".$table_prefix."posts.post_content LIKE %s)
                OR EXISTS (
                    SELECT * FROM ".$table_prefix."postmeta
                    WHERE post_id = ".$table_prefix."posts.ID AND (",
            $searchTerm,
            $searchTerm);

    //ADD THE SEARCH FILTERS FOR EACH ACF FIELD
    $where .= static::buildMetaFilters($searchTerm, $list_searcheable_acf);

    //CHECK COMMENTS AND TAXONMY TAGS
    $where .= $wpdb->prepare(")
        )
        OR EXISTS (
          SELECT * FROM ".$table_prefix."comments
          WHERE comment_post_ID = ".$table_prefix."posts.ID
            AND comment_content LIKE %s
        )
        OR EXISTS (
          SELECT * FROM ".$table_prefix."terms
          INNER JOIN ".$table_prefix."term_taxonomy
            ON ".$table_prefix."term_taxonomy.term_id = ".$table_prefix."terms.term_id
          INNER JOIN ".$table_prefix."term_relationships
            ON ".$table_prefix."term_relationships.term_taxonomy_id = ".$table_prefix."term_taxonomy.term_taxonomy_id
          WHERE (
                    taxonomy = 'post_tag'
                    OR taxonomy = 'category'
                    )
            AND object_id = ".$table_prefix."posts.ID
            AND ".$table_prefix."terms.name LIKE %s
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
  public static function buildMetaFilters($searchTerm, $list_searcheable_acf){
    global $wpdb;
    $where="";
    foreach ($list_searcheable_acf as $index=>$searcheable_acf) {

      //SET THE VALUE OF THE META KEY, THIS IS THE ACF FIELD WE WANT TO SEARCH IN.
      if (isset($searcheable_acf['meta_key'])){
        $meta_key = $searcheable_acf['meta_key'];
      } else {
        $meta_key = $searcheable_acf;
      }

      // SET THE VALUE OF THE COMPARE IF IT IS SET, OR SET IT TO '=' IF NONE IS SET
      if (isset($searcheable_acf['meta_compare'])){
        $meta_compare = $searcheable_acf['meta_compare'];
      } else {
        $meta_compare = '=';
      }

      // BUILD THE FILTER STRINGS
      if ($index==0){
        $where .= $wpdb->prepare(" (meta_key ".$meta_compare." %s AND meta_value LIKE %s) ", $meta_key, $searchTerm);
      } else {
        $where .= $wpdb->prepare(" OR (meta_key ".$meta_compare." %s AND meta_value LIKE %s) ", $meta_key, $searchTerm);
      }
    }

    return $where;
  }
}
