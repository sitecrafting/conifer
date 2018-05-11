# What is Conifer?

Conifer is a library plugin for WordPress that provides
simple but powerful abstractions many common tasks in
WordPress development.

These tasks include:

* Implementing custom post types
* Building advanced post queries
* Handling custom `wp_ajax_*` actions
* Implementing advanced navigation logic
* Processing forms with complex validation schemes
* Sending user/admin notifications
* Adding custom shortcodes
* Making post meta fields searchable

...and much more.

## Goals

Rather than aiming to be The One True Way to Do WordPress,
Conifer leans more toward The Way We Like to Do WordPress.
Like its authors, Conifer is opinionated. 
We hold certain values at the center our philosophy as
developers, designers, and creators. If you share these
values, we think you'll benefit from Conifer.

### Abstractions for a WordPress architecture

First and foremost, we wrote Conifer because we, like
many other programmers across all kinds of problem domains,
found ourselves solving the same problems from project to
project, over and over again. And like many web agencies,
we're not just in the Web Development business: We're in
the Problem-Solving business. We'd rather worry about
our clients' problems than about the nitty-gritty of
WordPress components we've wired together a million
times already.

What we realized we needed was a collection of _abstractions_.
Ways to compose many common, low-level pieces of WordPress
without having to worry about WordPress internals any more
than necessary. The low-level problems we were solving
were telling us something: that abstractions existed somewhere
in the way we work, and were crying to get out. We listened
to them. Out of this, Conifer was born.

### Object-Oriented

We believe that WordPress development can
(and often should) be done largely in the object-oriented style.
This stands in contrast to the way much (probably most)
WordPress development is done: relying heavily on global
state and defining public API functions in the global
namespace.

Because of how WordPress was designed, this is
unavavoidable to an extent. But we believe that a different
approach to WordPress can minimize global state and global
functions, and allow your PHP to be much more expressive
and flexible.

Conifer aims to be a safer, more encapsulated way to develop many common aspects of WordPress sites. Instead of global functions and variables, Conifer encourages the design of well-separated objects and interfaces.

### MVC-inspired

The Model-View-Controller (MVC) pattern is a powerful way to
write programs that have UIs for manipulating some internal
state. This includes web applications. Many hugely successful
web frameworks adopt this pattern as their central
architecture. We believe there's a good reason for this:
separation of concerns is a crucial aspect of any well-built
system, and the MVC pattern offers one of the best payoffs
when done right.

With all its qualities, one glaring fault of WordPress is that
it makes MVC very hard to do. The conventional way of building
WordPress templates mixes view and core business logic
everywhere. Its reliance on global state make it difficult
to encapsulate things. But with the right foundation, we think we can do
better.

Conifer aims to provide libraries for building independent
Model and View layers that fit within an MVC-flavored
WordPress architecture. With this architecture, it's much
easier to reason about your code in a way that feels like
a more conventionally written web application, with fewer
surprises.

### Timber all the way down

We don't take huge, central dependencies lightly. In fact,
relying on Timber was one of the things that got Conifer
rejected from the official WordPress plugin directory!
(The other thing was that as a library plugin, Conifer doesn't
"do anything" out of the box - a perfectly valid policy that
was adopted after Timber's acceptance into the directory.)

Timber offers _such_ a good foundation for WordPress development that once we started using it, we couldn't imagine ever doing custom WordPress work without it. While flexible, Timber is rather opinionated itself. Relative to the rest of the WordPress ecosystem, it is fairly aggressively object-oriented. We found that this aligned so well with our own opinions that we suddenly saw the possibility of pushing even further: to (eventually) develop a sort of standard library for OOP in WordPress, atop the foundation of Timber.

### Flexible

Like Timber, Conifer stays out of your way. You can use as much or as little of it as you like. If there are places where it just makes more sense to define a function in the global namespace, go for it. But when you want an OO interface for defining an AJAX handler, or complex form validations, or sending email alerts, or a whole host of other things, Conifer has your back.

## Not for Everyone

Why would you use Conifer, rather than just Timber (or vanilla WordPress)? Why a write separate plugin at all, instead of just adding more stuff to Timber? Two reasons.

First, Conifer aims to be even _more_ opinionated. It requires PHP 7.0+. It leverages PHP's new type-hinting features extensively. It takes the philosophy of Timber as a _premise_ and builds on it.

Second, Conifer aims to be _bigger_. Compared to Conifer, Timber aims to be a fairly thin, OO wrapper around core WordPress functionality. Conifer is a bit beefier, capturing abstractions across a wider range of functionality. For example, Timber introduces a convenient mechanism for adding Twig functions and filters to the view layer, along with a few choice implementations like the `Image` function. Conifer in turn provides a wealth of different functions and filters out of the box.

While we feel that Timber and Conifer's philosophies are extremely compatible, they are not identical. We didn't feel it would be appropriate or effective to impose our design decisions on the wider Timber community, and we didn't want to bloat the Timber codebase with more stuff than it needs in order to be the great foundation it already is.

So if you're content working with just Timber and don't need a lot beyond some basic Twig stuff or some simple Post logic, that's great! More power to you. But if you find yourself nodding along to these values and wishing Timber did _more_...meet Conifer. We think you'll get along.