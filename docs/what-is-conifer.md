# What is Conifer?

Conifer is a library plugin for WordPress that provides
simple but powerful abstractions many common tasks in
WordPress development.

## Goals

Rather than aiming to be The One True Way to Do WordPress,
Conifer leans more toward The Way We Like to Do WordPress.
We hold certain values to be central to our philosophy as
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
(and should) be done largely in the object-oriented style.
This stands in contrast to the way much (probably _most_)
WordPress development is done: relying heavily on global
state and defining public API functions in the global
namespace.

Because of how WordPress was designed, this is
unavavoidable to an extent. But we believe that a different
approach to WordPress can minimize global state and global
functions, and allow your PHP to be much more expressive
and flexible.

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
everywhere. Its reliance on global state make it very hard
to encapsulate things. So it's understandable that the current
state of affairs ends up involving SQL queries right next to
some HTML. But with the right foundation, we think we can do
better.

Conifer aims to provide libraries for building an
independent Model layer that fits within an MVC-flavored
WordPress architecture. With this architecture, it's much
easier to reason about your code in a way that feels like
a more conventionally written web application, with fewer
surprises.

### Timber all the way down

We don't take huge, central dependencies lightly. In fact,
relying on Timber was one of the things that got Conifer
rejected from the official WordPress plugin directory!
(The other thing was that as a library, Conifer doesn't
"do anything" out of the box.)