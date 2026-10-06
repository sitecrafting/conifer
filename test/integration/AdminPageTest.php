<?php

namespace Conifer\Integration;

use Timber\Timber;

class SimpleAdminPage extends \Conifer\Admin\Page
{
    public function render(array $data = []): string
    {
        $title = $data['title'] ?? 'Simple Admin Page';

        return "<h1>{$title}</h1>";
    }
}

class SimpleSubPage extends \Conifer\Admin\SubPage
{
    public function render(array $data = []): string
    {
        $title = $data['title'] ?? 'Simple Sub Page';

        return "<h1>{$title}</h1>";
    }
}

class AdminPageTest extends Base
{
    public function test_admin_page_render_without_data_argument()
    {
        $page = new SimpleAdminPage('Simple Admin');

        $this->assertSame('<h1>Simple Admin Page</h1>', $page->render());
    }

    public function test_admin_page_render_with_data_argument()
    {
        $page = new SimpleAdminPage('Simple Admin');

        $this->assertSame(
            '<h1>Custom Admin Title</h1>',
            $page->render(['title' => 'Custom Admin Title'])
        );
    }

    public function test_sub_page_render_without_data_argument()
    {
        $parent = new SimpleAdminPage('Parent Admin');
        $subPage = new SimpleSubPage($parent, 'Simple Sub');

        $this->assertSame('<h1>Simple Sub Page</h1>', $subPage->render());
    }

    public function test_sub_page_render_with_data_argument()
    {
        $parent = new SimpleAdminPage('Parent Admin');
        $subPage = new SimpleSubPage($parent, 'Simple Sub');

        $this->assertSame(
            '<h1>Custom Sub Title</h1>',
            $subPage->render(['title' => 'Custom Sub Title'])
        );
    }
}
