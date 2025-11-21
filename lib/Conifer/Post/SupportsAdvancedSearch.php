<?php

declare(strict_types=1);

/* @codingStandardsIgnoreFile */
namespace Conifer\Post;

use Conifer\Query\ClauseGenerator;

trait SupportsAdvancedSearch {
  /**
   * @param array[] $config
   */
  public static function configure_advanced_search(array $config): void {
    add_filter('posts_clauses', function(array $clauses, $query) use($config): array {
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
        fn(array $searchConfig): bool => array_intersect(
          $queryingPostTypes,
          $searchConfig['post_type']
        ) !== []);

      if ($searchCustomizations === []) {
        // no advanced search customizations apply to this query
        return $clauses;
      }

      // ->prepend_distinct
      $clauses['fields'] = ' DISTINCT ' . $clauses['fields'];

      // ->add_join('postmeta', 'posts.ID = postmeta.post_id')
      $clauses['join'] .=
        sprintf(' LEFT JOIN %s meta_search', $wpdb->postmeta)
        . sprintf(' ON ( %s.ID = meta_search.post_id ) ', $wpdb->posts);

      // map -> wildcard
      $terms = array_map(fn(string $term): string => sprintf('%%%s%%', $term), $query->query_vars['search_terms']);

      $whereClauses = array_map(function(array $postTypeSearch) use($wpdb, $terms, $query): string {
        $titleComparisons = array_map(fn(string $term): string => $wpdb->prepare(sprintf('%s.post_title LIKE %%s', $wpdb->posts), $term), $terms);
        $titleClause = '(' . implode(' OR ', $titleComparisons) . ')';

        $excerptComparisons = array_map(fn(string $term): string => $wpdb->prepare(sprintf('%s.post_excerpt LIKE %%s', $wpdb->posts), $term), $terms);
        $excerptClause = '(' . implode(' OR ', $excerptComparisons) . ')';

        $contentComparisons = array_map(fn(string $term): string => $wpdb->prepare(sprintf('%s.post_content LIKE %%s', $wpdb->posts), $term), $terms);
        $contentClause = '(' . implode(' OR ', $contentComparisons) . ')';

        $metaKeyComparisons = array_map(function($key) use($wpdb) : string {
          if (is_string($key)) {

            return $wpdb->prepare('(meta_search.meta_key = %s)', $key);

          } elseif (is_array($key) && isset($key['key'])) {

            $op = trim($key['key_compare'] ?? '=');

            if ($op !== 'LIKE') {
              $op = '=';
            }

            return $wpdb->prepare(sprintf('(meta_search.meta_key %s %%s)', $op), $key['key']);
          }

          return '';
        }, $postTypeSearch['meta_fields']);

        $metaKeyClause = '(' . implode(' OR ', $metaKeyComparisons) . ')';

        $metaValueComparisons = array_map(fn(string $term) => $wpdb->prepare('(meta_value LIKE %s)', $term), $terms);
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
        $postTypeCriteria = array_map(fn(string $type) => $wpdb->prepare('%s', $type), $postTypes);

        // get post status from current query
        $queryStatuses = $postTypeSearch['post_status'] ?? ['publish'];
        if ($queryStatuses === 'any') {
          $queryStatusClause = '';
        } else {
          $postStatuses = is_array($queryStatuses)
            ? $queryStatuses
            : [$queryStatuses];
          $postStatusCriteria = array_map(fn(string $type) => $wpdb->prepare('%s', $type), $postStatuses);

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
