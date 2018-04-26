# Testing

There are two main types of tests we run on the Conifer codebase: unit tests
and end-to-end tests. Unit tests are done with [PHPUnit](https://phpunit.de/),
while end-to-end tests are done with [Cypress](https://www.cypress.io/). Since
Conifer uses [Lando](https://docs.devwithlando.io/) as its official dev
environment, you can leverage both testing tools without having to install
them directly on your machine:

```
lando unit
lando e2e
```

It's worth mentioning that the Lando environment comes with built-in code-
sniffing, courtesy of `phpcs`:

```
lando sniff
```

## Writing new tests

Generally, new features should be covered by accompanying unit tests at a
minimum. Whenever possible/applicable, it's a good idea to also write end-to-
end tests in Cypress.

Our guidelines for how and what to test are:

* One unit test case class/file per library class. For example, a new
  `lib/Conifer/MyNewFeature.php` class file should be accompanied by a
  `test/cases/MyNewFeatureTest.php` class file.
* When in doubt about _what_ to test, test a class's _public interface_.
  Protected and private methods are implementation details and may change,
  but public interfaces should, by definition, remain consistent.
* Building on the previous guideline, tests should not care about
  implementation details. Specifically, this means you should avoid making
  assertions about internal state (e.g. protected properties). This helps avoid
  brittle tests that might break because of changing implentations, even as the
  public interface they're testing stays the same.
* Pull requests that include new library code but no tests will be treated as
  low-priority. We may ask you to add tests before accepting.
