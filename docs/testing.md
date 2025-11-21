# Testing

There are two main types of tests we run on the Conifer codebase: unit tests
and integration tests. Both types tests are performed with [PHPUnit](https://phpunit.de/).
The difference is just a `--group ...` option passed to PHPUnit when it is run.
Conifer uses [Lando](https://docs.devwithlando.io/) as its official dev
environment, you can leverage both testing tools without having to install
them directly on your machine:

```sh
lando unit
lando integration
```

Or run them all at once:

```sh
lando test
```

The Lando environment comes with built-in code-sniffing, courtesy of `phpcs`:

```sh
lando sniff
```

As of 1.0, static analysis by PHPStan is also available:

```sh
lando analyze
```

As of 1.0, Rector is also included:

```shell
lando rector
```

## Writing new tests

Our guidelines for how and what to test are:

* One unit test case class/file per library class. For example, a new
  `lib/Conifer/MyNewFeature.php` class file should be accompanied by a
  `test/cases/MyNewFeatureTest.php` class file.
* When in doubt about _what_ to test, test a class's _public interface_.
  Protected and private methods are implementation details and may change,
  but public interfaces should, by definition, remain consistent.
* Don't test implementation details. In other words, don't make
  assertions about internal state, such as protected properties. This helps avoid
  brittle tests that might break because of changing implementations, even as the
  public interface they're testing stays the same.
* Use [WP_Mock](https://github.com/10up/wp_mock) for mocking core WP calls. In other scenarios, avoid using mocks when possible: use real objects instead. If you have to mock several different things just to make an assertion about a single method, that indicates you may need to redesign your code.
* Pull requests that include new library code but no tests will be treated as
  low-priority. We may ask you to add tests before accepting.
* Exceptions to these guidelines are OK given reasonable justification.
