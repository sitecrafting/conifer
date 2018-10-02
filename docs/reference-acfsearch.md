
### Class: \Conifer\AcfSearch

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>advanced_custom_search(</strong><em>\Conifer\query-part/string</em> <strong>$where</strong>, <em>object</em> <strong>$wp_query</strong>)</strong> : <em>query-part/string $where the "where" part of the search query as we customized see https://vzurczak.wordpress.com/2013/06/15/extend-the-default-wordpress-search/ credits to Vincent Zurczak for the base query structure/spliting tags section</em><br /><em>advanced_custom_search search that encompasses ACF/advanced custom fields and taxonomies and split expression before request</em> |
| public static | <strong>buildMetaFilters(</strong><em>string</em> <strong>$searchTerm</strong>, <em>array</em> <strong>$list_searcheable_acf</strong>)</strong> : <em>string $where part of the where clause</em><br /><em>Build the WHERE clause for each ACF field to include in our search query</em> |
| public static | <strong>buildWhereClause(</strong><em>array</em> <strong>$list_searcheable_acf</strong>, <em>string</em> <strong>$tag</strong>)</strong> : <em>string $where where clause</em><br /><em>buildWhereClause build the where string to include in our search query</em> |
| public static | <strong>list_searcheable_acf()</strong> : <em>array list of custom fields</em><br /><em>list_searcheable_acf list all the custom fields we want to include in our search query</em> |

