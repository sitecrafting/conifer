<?php

/**
 * Conifer\Admin\Page class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace Conifer\Admin;

/**
 * Class for abstracting WP Admin pages
 *
 * @example
 * ```php
 * use Conifer\Admin\Page as AdminPage;
 * use Conifer\Admin\SubPage;
 *
 * class MySettingsPage extends AdminPage {
 *   public function render() : string {
 *     return '<h1>This is the top-level settings page</h1>';
 *   }
 * }
 *
 * class MoreSettingsPage extends SubPage {
 *   public function render() : string {
 *     return '<h1>This is a second-tier settings page</h1>';
 *   }
 * }
 *
 * $page = new MySettingsPage('My Theme Settings');
 *
 * // add your pages like this...
 * $page
 *   ->add()
 *   ->add_sub_page(MoreSettingsPage::class, 'More Theme Settings');
 *
 * // ...or like this:
 * $page->add();
 * $subPage = new MoreSettingsPage($page, 'More Theme Settings');
 * $subPage->add();
 * ```
 */
abstract class SubPage extends Page {
  /**
   * The parent Page
   *
   * @var Page
   */
  protected $parent;

  /**
   * Constructor
   *
   * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
   * @param Page $parent the parent Admin Page, which provides the `parent_slug`
   * for this submenu page.
   * @param string $title the `page_title` to use as the <title> element text
   * on this SubPage.
   * @param string $menuTitle the title to display for this SubPage in the
   * admin menu.
   * @param string $capability the WP capability required to view this SubPage.
   * Defaults to the capabaility set on the parent AdminPage.
   * @param string $slug the `menu_slug` for this SubPage.
   */
  public function __construct(
    Page $parent,
    string $title,
    string $menuTitle = NULL,
    string $capability = NULL,
    string $slug = NULL
  ) {
    $this->parent = $parent;

    parent::__construct(
      $title,
      $menuTitle,
      $capability ?: $parent->get_capability(),
      $slug
    );
  }

  /**
   * Add this SubPage to the WP Admin menu
   *
   * @return Page returns this SubPage
   */
  public function add() : Page {
    add_action('admin_menu', [$this, 'do_add']);
    return $this;
  }

  /**
   * The callback to the `admin_menu` action.
   *
   * @return Page returns this SubPage
   */
  public function do_add() : Page {
    $renderCallback = function() {
      // NOTE: Since render() is specifically for outputting HTML in the admin
      // area, users are responsible for escaping their own output accordingly.
      // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
      echo $this->render($this->slug);
    };

    add_submenu_page(
      $this->parent->get_slug(),
      $this->title,
      $this->menu_title,
      $this->capability,
      $this->slug,
      $renderCallback
    );

    return $this;
  }
}
