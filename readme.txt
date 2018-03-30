=== Conifer ===
Contributors: acobster
Tags: twig
Requires at least: 4.7.9
Tested up to: 4.9.4
Stable tag: 0.1.0
PHP version: 7.0 or greater
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Powerful abstractions for serious WordPress development.

== Description ==

Conifer is a opinionated framework for building complex WordPress sites in the
Object-Oriented style. It is built on top of the amazing
[Timber plugin](https://www.upstatement.com/timber/). **Therefore, Conifer
requires Timber.**

This plugin is in active development. Documentation is a work in progress.

== Installation ==

First, you **must install and activate [Timber](https://www.upstatement.com/timber/).**
You will not benefit from Conifer at all without it.

After both Timber and Conifer are installed and activated from your site's
plugins page, try it out in your theme's `functions.php`:

```
use Conifer\Site;

$site = new Site();
$site->configure();
```

If you've used Timber before, this should look pretty familiar. One notable
difference is that, unlike with vanilla Timber, no business logic happens in
the constructor. Calling `$site->configure()` without arguments sets up some
defaults for you.

Now let's set up a template that actually uses Conifer's API!

```
// front-page.php

use Conifer\Post\FrontPage;

// Create an object specifically for representing the homepage
$page = FrontPage::get();

// Call helper for getting the Timber context,
// and set the "post" within that context
$data = $site->get_context_with_post($page);

Timber::render('front-page.twig', $data);
```

Create a Twig template to render within your theme's `views` directory (where
Timber looks by default):

```
<h1>{{ post.title }}</h1>

<div class="post-content">{{ post.content }}</div>
```

This is just scratching the surface of what Conifer provides. Its true power
lies in the powerful object-oriented API it exposes for doing things like
complex navigation state, simple email notifiers, metafield search, AJAX
handlers, shortcodes, breadcrumbs, content authorization levels, and more.
