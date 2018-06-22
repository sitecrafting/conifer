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
   * @param string $slug the `menu_slug` for this SubPage.
   */
  public function __construct(
    Page $parent,
    string $title,
    string $menuTitle = '',
    string $capability = '',
    string $slug = ''
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
