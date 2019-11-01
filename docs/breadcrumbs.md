# Breadcrumb Navigation

In the folk tale of Hansel and Gretel, the titular characters leave a trail of breadcrumbs to help them find their way back through the forest, away from the creepy stranger who offers them candy. On the web, the (hopefully) lower-stakes version of this comes in the form of breadcrumb navigation, often shortened to just "breadcrumbs." This is common, user-friendly pattern illustrates the path the user took through the navigation hierarchy to get to the current page. In HTML, it might look something like this:

```html
<nav class="breadcrumb-nav">
  <a href="/">Home</a>
  <a href="/forest">Forest</a>
  <a href="/forest/trees">Trees</a>
  <a href="/forest/trees/glade">Glade</a>
</nav>
```

For a static site, it's fine to just code your breadcrumbs this way and leave it at that. But for server-side web development work, where you may not know the final information architecture (and maybe can't ever know, because the site might change at any time), the limitations of this approach become clear.

Fortunately, WordPress's nav menus and Timber's `Menu` class provide a way to dynamically display nav structure in a way that conveys the user's location in-context. But you still have to loop through nav items manually, and the logic doesn't tend to vary a whole lot between projects.

Conifer offers an abstraction around a few common types of navigation hierarchy. Let's start with the most basic: the hierarchy of the menu items themselves.

## Breadcrumbs from Navigation Menus

Say you have a WordPress menu with the following structure:

```
/
├── /forest
│   ├── /shrubs
│   │   ├── /copse
│   │   ├── /glade
│   │   └── /thicket
│   └── /trees
│       ├── /copse
│       ├── /glade
│       └── /thicket
└── /town
    ├── /downtown
    │   ├── /the-station
    │   └── /da-club
    └── /suburbs
        ├── /houses
        └── /schools
```

Let's assume for simplicity that each of these pages uses the default template. This is all it takes to get a context-aware breadcrumb on your page:

```php
/* page.php */
use Conifer\Navigation\NavBreadcrumbTrail;
use Conifer\Post\Page;
use Timber\Timber;

$data = $site->get_context_with([
  'post'             => new Page(),
  'breadcrumb_trail' => new NavBreadcrumbTrail(),
]);

Timber::render('page.twig', $data);
```

Then, in your Twig view, just call the `breadcrumbs(...)` method on your breadcrumb trail object:

```twig
{# page.twig #}

<nav class="breadcrumb-nav">
  {% for crumb in breadcrumb_trail.breadcrumbs(post) %}
  	<a href="{{ crumb.link }}">{{ crumb.title }}</a>
  {% endfor %}
</nav>
```

That's it.

## Breadcrumbs from Post Hierarchy

If your site structure relies heavily on post hierarchy (i.e. parent/child relationships between posts, as set in the `wp_posts.post_parent` database column) rather than your navigation structure, the `PostBreadcrumbTrail` class is your friend.

Usage is nearly identical to the example above. Just switch out the class, and your breadcrumbs will honor the post hierarchy instead:

```php
/* page.php */
use Conifer\Navigation\PostBreadcrumbTrail;        // <-- use a different class...
use Conifer\Post\Page;
use Timber\Timber;

$data = $site->get_context_with([
  'post'             => new Page(),
  'breadcrumb_trail' => new PostBreadcrumbTrail(), // <-- ...and instatiate it here
]);

Timber::render('page.twig', $data);
```

Your Twig code can remain exactly the same.

## Breadcrumbs from Term Hierarchy

Another common use-case for breadcrumbs is hierarchical taxonomies, such as categories. For breadcrumb structures that mirror these hierarchies, Conifer provides the `TaxonomyBreadcrumbTrail` class. This work in much the same way as `PostBreadcrumbTrail`:

```php
/* page.php */
use Conifer\Navigation\TermBreadcrumbTrail;        // <-- again, just use the class...
use Conifer\Post\Page;
use Timber\Timber;

$data = $site->get_context_with([
  'post'             => new Page(),
  'breadcrumb_trail' => new TermBreadcrumbTrail(), // <-- ...and instatiate it here
]);

Timber::render('page.twig', $data);
```

And again, your Twig can stay as-is. 🚀

## Composing custom Breadcrumb structures

All the `*BreadcrumbTrail` classes we've seen so far implement a single interface: the `BreadcrumbTrailInterface`. To make your own, all you have to do is implement your own `breadcrumbs` method that takes some `Timber\Core` object (e.g. a `Menu` or a `Post`) and return something `Iterable`:

```php
interface BreadcrumbTrailInterface {
  public function breadcrumbs(Core $core = null) : Iterable;
}
```

Let's imagine you have some specific piece of information architecture that's not easily captured in a Menu or a hierarchy of posts/terms. Say, for example, that you run the Wicked Yummy Kids' Kandy Shop online store. So yummy! Your breadcrumb structure is a hybrid that depends on some well-known structure at the top: **Home**, a **Candy** landing page, and a **Candy Finder**. Below that, you have an arbitrarily nested hierarchy of different candy product posts. So your overall structure might end up looking something like this:

```
/
└── /candy
    └── /candy-finder
        ├── /chocolate
        │   ├── /wicked-milky-chocolate
        │   └── /dark-magic-chocolate
        └── /hard-candy
            ├── /jolly-ranchers
            └── /poor-unfortunate-ranchers
```

When visiting a page higher up in the hierarchy, such as **Candy** or the **Candy Finder**, you only want the trail  to extend that far, and no further:

```html
<nav class="breadcrumb-nav">
  <a href="/">Home</a>
  <a href="/candy">Candy</a>
  <!-- we only want this to show on the Candy Finder page or below: -->
  <a href="/candy/candy-finder">Candy Finder</a>
</nav>
```

Now say a user navigates to **Poor Unfortunate Ranchers** (it's sad...but true). So you'd want the rendered breadcrumb nav to looks something like this:

```html
<nav class="breadcrumb-nav">
  <a href="/">Home</a>
  <a href="/candy">Candy</a>
  <a href="/candy/candy-finder">Candy Finder</a>
  <a href="/candy/hard-candy">Hard Candy</a>
  <a href="/candy/hard-candy/poor-unfortunate-ranchers">Poor Unfortunate Ranchers</a>
</nav>
```

Notice here that the nav structure isn't coupled to the URL structure. It doesn't have to be. You get a hybrid of pages, archives, and product posts that have their own independent structure.

To achieve this, we'll actually want a few different classes to help us. This will seem like a lot of code, but stick with us, and we you'll hope see by the time we're done that this provides a nice architecture that can scale well with more complex needs.

Let's start by defining some common code that every page will use. We can include this as a partial from each template, or put it in a layout that each file will extend, whichever works best for you. The point is that it only makes one assumption: that there's a `breadcrumb_trail` object defined in the Twig:

```twig
{# breadcrumb-nav.twig #}

<nav class="breadcrumb-nav">
  {% for crumb in breadcrumb_trail.breadcrumbs(post) %}
  	<a href="{{ crumb.link }}">{{ crumb.title }}</a>
  {% endfor %}
</nav>
```

This should look familiar because it's the exact same code as the very first example up above. The only difference is that it lives in a partial, so that we can pass it different objects for `breadcrumb_trail`.

With that out of the way, let's build our first custom class, which we'll use in our **Candy Archive** template:

```php
namespace WickedYummy\Navigation;

use Conifer\Navigation\BreadcrumbTrailInterface;
use Conifer\Post\FrontPage;
use Timber\Core;

class CandyArchiveBreadcrumbTrail implements BreadcrumbTrailInterface {
	public function breadcrumbs(Core $product = null) : Iterable {
    return [
      FrontPage::get(), // home page
      ['link' => '/candy', 'title' => 'Candy'], // archive page; not a real WP post
    ];
  }
}
```

Next, let's define a class to use in the **Candy Finder** template:

```php
namespace WickedYummy\Navigation;

use Conifer\Navigation\BreadcrumbTrailInterface;
use Conifer\Post\Page;
use Timber\Core;

class CandyFinderBreadcrumbTrail
  extends CandyArchiveBreadcrumbTrail
  implements BreadcrumbTrailInterface {

	public function breadcrumbs(Core $product = null) : Iterable {
    // stick the Candy Finder page on the end of the breadcrumb trail "above" us
    return array_merge(parent::breadcrumbs(), [
      Page::get_by_template('candy-finder.php'),
    ]);
  }
}
```

Finally, we can leverage both of these classes for the dynamic post-hierarchy piece, the candy products:

```php
namespace WickedYummy\Navigation;

use Conifer\Navigation\BreadcrumbTrailInterface;
use Conifer\Navigation\PostBreadcrumbTrail;
use Conifer\Post\FrontPage;
use Conifer\Post\Page;
use Timber\Core;

class SingleCandyBreadcrumbTrail
  extends CandyFinderBreadcrumbTrail
  implements BreadcrumbTrailInterface {
  
  protected $product_trail;
  
  public function __construct() {
    // we'll use this to compute the post-hiearchy portion of the trail
    $this->product_trail = new PostBreadcrumbTrail();
  }

	public function breadcrumbs(Core $product = null) : Iterable {
    // climb up the candy hierarchy...
    $productHierarchy = $this->product_trail->breadcrumbs($product);

    // ...and stick it on the end of the entire trail.
    return array_merge(parent::breadcrumbs(), $productHierarchy);
  }
}
```

Note that our class hierarchy corresponds to the breadcrumb hierarchy. This isn't a coincidence! It means that if we ever want to change the top levels, for example insert a "crumb" between **Home** and **Candy**, we only need to do that within `CandyArchiveBreadcrumbTrail`. The rest will inherit this change.

Now, within our template code, all we have to do is instantiate the right class:

```php
/* archive-candy.php */
$data['breadcrumb_trail'] = new CandyArchiveBreadcrumbTrail();

/* candy-finder.php */
$data['breadcrumb_trail'] = new CandyFinderBreadcrumbTrail();

/* single-candy.php */
$data['breadcrumb_trail'] = new SingleCandyBreadcrumbTrail();
```

None of our Twig code needs to change to accommodate this.