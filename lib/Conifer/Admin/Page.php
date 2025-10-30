<?php

declare(strict_types=1);

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
 *
 * class MySettingsPage extends AdminPage {
 *   public function render() : string {
 *     return '<h1>ALL THE SETTINGS</h1> ...';
 *   }
 * }
 *
 * $settingsPage = new MySettingsPage('My Theme Settings');
 * $settingsPage->add();
 * ```
 */
abstract class Page {
  /**
   * The menu_title
   */
  protected string $menu_title;

  /**
   * The menu_slug
   *
   * @var string
   */
  protected string $slug;

  /**
   * Render the content of this admin Page.
   *
   * @param array<string, string> $data optional view data for rendering in a specific context
   * @return string
   */
  abstract public function render(array $data = []) : string;

  /**
   * Constructor
   *
   * @see https://developer.wordpress.org/reference/functions/add_menu_page/
   * @param string $title the page_title for this page
   * @param string $menuTitle the menu_title for this Page.
   * Defaults to `$title`
   * @param string $capability the capability required to view this Page.
   * Defaults to `"manage_options"`.
   * @param string $slug the menu_slug for this Page.
   * Defaults to the sanitized `$menuTitle`.
   * @param string $icon_url the icon_url for this Page
   */
  public function __construct(
    protected string $title,
    string $menuTitle = '',
    protected string $capability = 'manage_options',
    string $slug = '',
    protected string $icon_url = ''
  ) {
    $this->menu_title = $menuTitle ?: $this->title;
    $this->slug       = $slug ?: sanitize_key($this->menu_title);
  }

  /**
   * Add this Admin Page to the admin main menu
   *
   * @return Page returns this Page
   */
  public function add() : Page {
    add_action('admin_menu', $this->do_add(...));

    return $this;
  }

  /**
   * The callback to the `admin_menu` action.
   */
  public function do_add(): Page {
    $renderCallback = function(): void {
      // NOTE: Since render() is specifically for outputting HTML in the admin
      // area, users are responsible for escaping their own output accordingly.
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      echo $this->render(['slug' => $this->slug]);
    };

    add_menu_page(
      $this->title,
      $this->menu_title,
      $this->capability,
      $this->slug,
      $renderCallback,
      $this->icon_url
    );

    return $this;
  }

  /**
   * Add a sub-menu admin page to this Page.
   *
   * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
   * @param string $class the `SubPage` child class to instantiate to
   * represent the new sub-page.
   * @param string $title the page_title for the sub-page
   * @param string $menuTitle the menu_title for the sub-page.
   * Defaults to `$title`.
   * @param string $capability the capability required for viewing the sub-page.
   * Defaults to the required capability for this Page.
   * @param string $slug the menu_slug for the sub-page.
   * Defaults to the sanitized `$menuTitle`.
   * @return Page returns this Page.
   */
  public function add_sub_page(
    string $class,
    string $title,
    string $menuTitle = '',
    string $capability = '',
    string $slug = ''
  ) : self {
    $page = new $class($this, $title, $menuTitle, $capability, $slug);
    $page->add();

    return $this;
  }

  /**
   * Get the `page_title` to be passed to WP when this Page is added.
   *
   * @see https://developer.wordpress.org/reference/functions/add_menu_page/
   * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
   * @return string
   */
  public function get_title() : string {
    return $this->title;
  }

  /**
   * Get the `menu_title` to be passed to WP when this Page is added.
   *
   * @see https://developer.wordpress.org/reference/functions/add_menu_page/
   * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
   * @return string
   */
  public function get_menu_title() : string {
    return $this->menu_title;
  }

  /**
   * Get the `capability` to be passed to WP when this Page is added.
   *
   * @see https://developer.wordpress.org/reference/functions/add_menu_page/
   * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
   * @return string
   */
  public function get_capability() : string {
    return $this->capability;
  }

  /**
   * Get the `menu_slug` to be passed to WP when this Page is added.
   * When adding sub-pages, this is what is passed as `parent_slug`
   *
   * @see https://developer.wordpress.org/reference/functions/add_menu_page/
   * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
   * @return string
   */
  public function get_slug() : string {
    return $this->slug;
  }

  /**
   * Get the `icon_url` to be passed to WP when this Page is added.
   *
   * @see https://developer.wordpress.org/reference/functions/add_menu_page/
   * @return string
   */
  public function get_icon_url() : string {
    return $this->icon_url;
  }

  /**
   * Set the slug for this Admin Page.
   *
   * @param string $slug the menu_slug for this Page
   * @return Page returns this Page object
   */
  public function set_slug(string $slug) : Page {
    $this->slug = $slug;
    return $this;
  }

  /**
   * Set the menu_title for this Admin Page.
   *
   * @param string $menuTitle the title to display in the Admin menu
   * @return Page returns this Page object
   */
  public function set_menu_title(string $menuTitle) : Page {
    $this->menu_title = $menuTitle;
    return $this;
  }

  /**
   * Set the capability required to view this Admin Page.
   *
   * @param string $capability the WP capability string, e.g. "edit_posts"
   * @return Page returns this Page object
   */
  public function set_capability(string $capability) : Page {
    $this->capability = $capability;
    return $this;
  }

  /**
   * Set the title for this Admin Page.
   *
   * @param string $title the <title> element text to display on this Admin Page
   * @return Page returns this Page object
   */
  public function set_title(string $title) : Page {
    $this->title = $title;
    return $this;
  }

  /**
   * Set the icon_url for this Admin Page.
   *
   * @param string $url the icon_url to be displayed in the Menu for this
   * Admin Page.
   *
   * @return Page returns this Page object
   */
  public function set_icon_url(string $url) : Page {
    $this->icon_url = $url;
    return $this;
  }
}
