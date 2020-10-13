<?php

/* @codingStandardsIgnoreFile */
namespace Conifer\Post;

use Conifer\Query\ClauseGenerator;

trait SupportsAdvancedSearch {
  public static function configure_advanced_search(array $config) {
    //$modifier  = new QueryModifier($GLOBALS['wpdb']);

    add_filter('posts_clauses', function(array $clauses, $query) use($config) {
      global $wpdb;

      //debug($query->meta_query->queries);
      if (!$query->is_search() || empty($query->query_vars['search_terms'])) {
        // nothing to do
        return $clauses;
      }

      // query by post_type
      $queryingPostTypes = $query->query_vars['post_type'] ?? [];
      if (!is_array($queryingPostTypes)) {
        $queryingPostTypes = [$queryingPostTypes];
      }

      // customize only queries for post_types that appear in config
      $searchCustomizations = array_filter(
        $config,
        function($searchConfig) use($queryingPostTypes) {
          return !empty(array_intersect(
            $queryingPostTypes,
            $searchConfig['post_type']
          ));
        });

      if (empty($searchCustomizations)) {
        // no advanced search customizations apply to this query
        return $clauses;
      }

      // ->prepend_distinct
      $clauses['fields'] = ' DISTINCT ' . $clauses['fields'];

      // ->add_join('postmeta', 'posts.ID = postmeta.post_id')
      $clauses['join'] .=
        " LEFT JOIN {$wpdb->postmeta} meta_search"
        . " ON ( {$wpdb->posts}.ID = meta_search.post_id ) ";

      // map -> wildcard
      $terms = array_map(function(string $term) : string {
        return "%{$term}%";
      }, $query->query_vars['search_terms']);

      $whereClauses = array_map(function(array $postTypeSearch) use($wpdb, $terms, $query) {
        $titleComparisons = array_map(function(string $term) use($wpdb) : string {
          return $wpdb->prepare("{$wpdb->posts}.post_title LIKE %s", $term);
        }, $terms);
        $titleClause = '(' . implode(' OR ', $titleComparisons) . ')';

        $excerptComparisons = array_map(function(string $term) use($wpdb) : string {
          return $wpdb->prepare("{$wpdb->posts}.post_excerpt LIKE %s", $term);
        }, $terms);
        $excerptClause = '(' . implode(' OR ', $excerptComparisons) . ')';

        $contentComparisons = array_map(function(string $term) use($wpdb) : string {
          return $wpdb->prepare("{$wpdb->posts}.post_content LIKE %s", $term);
        }, $terms);
        $contentClause = '(' . implode(' OR ', $contentComparisons) . ')';

        $metaKeyComparisons = array_map(function($key) use($wpdb) : string {
          if (is_string($key)) {

            return $wpdb->prepare('(meta_search.meta_key = %s)', $key);

          } elseif (is_array($key) && isset($key['key'])) {

            $op = trim($key['key_compare'] ?? '=');

            if ($op !== 'LIKE') {
              $op = '=';
            }

            return $wpdb->prepare("(meta_search.meta_key {$op} %s)", $key['key']);
          }

          return '';
        }, $postTypeSearch['meta_fields']);

        $metaKeyClause = '(' . implode(' OR ', $metaKeyComparisons) . ')';

        $metaValueComparisons = array_map(function(string $term) use($wpdb) {
          return $wpdb->prepare('(meta_value LIKE %s)', $term);
        }, $terms);
        $metaValueClause = '(' . implode(' OR ', $metaValueComparisons) . ')';

        $metaClause = ' (' . implode(' AND ', [$metaKeyClause, $metaValueClause]) . ')';

        // put it all together
        $searchClauses = [$titleClause, $excerptClause, $contentClause, $metaClause];

        // get post types from current query
        $queryPostType = $query->query_vars['post_type'];

        // support post_type wildcard "any"
        if ($queryPostType === 'any') {
          $queryPostType = get_post_types(['public' => true], 'names');
        }

        // ensure we have an array to map over
        $postTypes     = is_array($queryPostType)
          ? $queryPostType
          : [$queryPostType];
        $postTypeCriteria = array_map(function(string $type) use($wpdb) {
          return $wpdb->prepare('%s', $type);
        }, $postTypes);

        // get post status from current query
        $queryStatuses = $postTypeSearch['post_status'] ?? ['publish'];
        if ($queryStatuses === 'any') {
          $queryStatusClause = '';
        } else {
          $postStatuses = is_array($queryStatuses)
            ? $queryStatuses
            : [$queryStatuses];
          $postStatusCriteria = array_map(function(string $type) use($wpdb) {
            return $wpdb->prepare('%s', $type);
          }, $postStatuses);

          $queryStatusClause = ' AND wp_posts.post_status IN ('
            . implode(', ', $postStatusCriteria)
            . ')';
        }

        return
          '('

          . '(' . implode(' OR ', $searchClauses) . ')'

          . ' AND wp_posts.post_type IN (' . implode(', ', $postTypeCriteria) . ')'

          . $queryStatusClause

          . ')';
      }, $config);

      $clauses['where'] = ' AND (' . implode(' OR ', $whereClauses) . ')';

      // defer to WP default orderby clause for now

      return $clauses;
    }, 10, 2);
  }
}
