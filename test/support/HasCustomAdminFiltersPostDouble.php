<?php

namespace Conifer\Unit\Support;

use Conifer\Unit\Support\Person;

class HasCustomAdminFiltersPostDouble extends Person
{
    /**
     * @var array<int, array<string, mixed>>
     */
    protected static array $renderedFilters = [];

    protected static function render_custom_filter_select(array $data)
    {
        static::$renderedFilters[] = $data;
    }

    public static function resetRenderedFilters(): void
    {
        static::$renderedFilters = [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function getRenderedFilters(): array
    {
        return static::$renderedFilters;
    }

    public static function allow_custom_filtering_public(): bool
    {
        return static::allow_custom_filtering();
    }

    public static function querying_by_custom_filter_public(string $name, \WP_Query $query): bool
    {
        return static::querying_by_custom_filter($name, $query);
    }

    public static function get_taxonomy_label_public(string $tax): string
    {
        return static::get_taxonomy_label($tax);
    }
}
