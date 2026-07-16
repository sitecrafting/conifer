<?php

namespace Conifer\Unit\Support;

use Conifer\Post\Post;
use Conifer\Unit\Support\Person;

class HasCustomAdminColumnsPostDouble extends Person
{
    /**
     * @var array<int, array<string, mixed>>
     */
    protected static array $renderedColumns = [];

    protected static function render_custom_column(array $data)
    {
        static::$renderedColumns[] = $data;
    }

    public static function resetRenderedColumns(): void
    {
        static::$renderedColumns = [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function getRenderedColumns(): array
    {
        return static::$renderedColumns;
    }

    // Overridden private functions from HasCustomAdminColumns trait to make them public for testing
    public static function value_getter_public(string $key): callable
    {
        /** @var callable */
        return static::invokePostPrivateStaticMethod('value_getter', [$key]);
    }

    public static function post_meta_getter_public(string $key): callable
    {
        /** @var callable */
        return static::invokePostPrivateStaticMethod('post_meta_getter', [$key]);
    }

    public static function page_template_name_public(int $postId): string
    {
        /** @var string */
        return static::invokePostPrivateStaticMethod('page_template_name', [$postId]);
    }

    /**
     * @param array<int, mixed> $args
     * @return mixed
     */
    protected static function invokePostPrivateStaticMethod(string $method, array $args = [])
    {
        $reflection = new \ReflectionMethod(Post::class, $method);

        return $reflection->invokeArgs(null, $args);
    }
}
