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
      if ($query->is_search()) {
        // ->prepend_distinct
        $clauses['fields'] = ' DISTINCT ' . $clauses['fields'];

        // ->add_join('postmeta', 'posts.ID = postmeta.post_id')
        $clauses['join'] .=
          " LEFT JOIN {$wpdb->postmeta}"
          . " ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id ) ";

        // map -> wildcard
        $terms = array_map(function(string $term) : string {
          return "%{$term}%";
        }, $query->query_vars['search_terms']);

        $whereClauses = array_map(function(array $postTypeSearch) use($wpdb, $terms) {
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

          $metaKeyComparisons = [
            '(meta_key = "hello")',
            '(meta_key LIKE "good%")',
          ];
          $metaKeyComparisons = array_map(function($key) use($wpdb) : string {
            if (is_string($key)) {

              return $wpdb->prepare('(meta_key = %s)', $key);

            } elseif (is_array($key) && isset($key['key'])) {

              $op = trim($key['key_compare'] ?? '=');

              if ($op !== 'LIKE') {
                $op = '=';
              }

              return $wpdb->prepare("(meta_key {$op} %s)", $key['key']);
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

          // TODO default to get_post_types() or similar
          $postTypes = $postTypeSearch['post_type'] ?? ['post', 'page'];
          $postTypeCriteria = array_map(function(string $type) use($wpdb) {
            return $wpdb->prepare('%s', $type);
          }, $postTypes);

          // TODO default to get_post_statues() or similar
          $postStatuses = $postTypeSearch['post_status'] ?? ['publish'];
          $postStatusCriteria = array_map(function(string $type) use($wpdb) {
            return $wpdb->prepare('%s', $type);
          }, $postStatuses);

          return
            '('

            . '(' . implode(' OR ', $searchClauses) . ')'

            . ' AND wp_posts.post_type IN (' . implode(', ', $postTypeCriteria) . ')'

            . ' AND wp_posts.post_status IN (' . implode(', ', $postStatusCriteria) . ')'

            . ')';
        }, $config);

        $clauses['where'] = ' AND (' . implode(' OR ', $whereClauses) . ')';
      }
      add_filter('query', function($sql) { error_log($sql); return $sql; });

      return $clauses;
    }, 10, 2);
  }
}
