# Development Setup

[Lando](https://docs.devwithlando.io) is the official Conifer development environment, and comes highly recommended. It's the best local dev tool in the galaxy!

First, [install Lando](https://docs.devwithlando.io/installation/installing.html) if you haven't already. Your future self will thank you.

### Clone

```bash
git clone git@github.com:sitecrafting/conifer.git
```

**Note:** Lando setups are self-contained, so it doesn't matter where you clone it to (as it would if using Vagrant, MAMP, or similar).

### Start

```bash
cd conifer
lando start
```

The first time you start the app, it will install WordPress, Timber, and the Groot starter theme for you. Then it will expose your local Conifer dev site at https://conifer.lndo.site (or similiar).

WP Admin username: **conifer**

Password: **conifer**

### That's it

Yup, that's pretty much all there is to it.

Recommended next steps:

* If you're new to Lando, we suggest reading the [Overview](https://docs.devwithlando.io/), and then the [WordPress recipe tutorial](https://docs.devwithlando.io/tutorials/wordpress.html). Conifer uses the WordPress recipe under the hood, so it's useful to know what's going on.
* Run `lando` with no arguments to see/verify custom tooling commands (also see **Custom Lando Tooling**, below)
* Log in to /wp-admin to verify the credentials you set up
* Explore the [testing](/testing.md) setup
* Start hacking on Conifer! 🌲 🚀 🎉

### Custom Lando Tooling

Conifer's Lando environment comes with several goodies for making local development a breeze.

#### CLI commands

Along with the [universal Lando commands](https://docs.devwithlando.io/cli/usage.html), we get these commands for free from Lando's built-in [WordPress recipe](https://docs.devwithlando.io/tutorials/wordpress.html):

* `lando wp`: run [WP-CLI](https://wp-cli.org/) commands
* `lando composer`: run composer commands
* `lando db-import <file>`: Import a WordPress database from a .sql or .sql.gz file. **The file must be inside your Conifer directory tree!**
* `lando php`: run arbitrary PHP commands
* `lando mysql [wordpress]`: drop into a MySQL shell (`wordpress` is the name of the database Lando installs for us)

Conifer's Lando setup also provides these commands:

* `lando unit`: run Conifer's PHPUnit test suite
* `lando sniff`: run [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer) on the Conifer codebase
* `lando analyze`: run [PHPStan](https://phpstan.com) static analysis on the Conifer codebase
* `lando debug`: `tail` the WP debug.log in real time
* `lando yarn`: run arbitrary [yarn](https://www.npmjs.com/package/yarn) commands
* `lando docs`: build the Conifer doc site being served from the `docs` service (https://docs.conifer.lndo.site or similar)
* `lando gitbook`: Run arbitrary [gitbook](https://github.com/GitbookIO/gitbook) commands.

#### Serving a local documentation site

If you're working on docs, it's helpful to see the doc site as compiled from your exact copy of the Markdown files.

To that end, you can run `lando docs` to build the docs site once.

It's annoying to do that over and over, though. To rebuild the docs automatically whenever a Markdown file changes, run `lando gitbook serve`. This will start a process and block your current shell session; press `Ctrl-C` to exit the process.

> #### Warning::The `localhost` URL in the output is misleading
>
> The `gitbook serve` command starts a web server at `localhost:4000`, but this is actually the `localhost` inside the `docs` service container, meaning you can't actually view the doc site at that address. Go to the proxy URL output by `lando start` or `lando info` instead. This will be something like https://docs.conifer.lndo.site.

#### Mailhog

Conifer's Lando environment also includes a [Mailhog](https://github.com/mailhog/MailHog) instance for catching and managing outgoing mail at https://mailhog.conifer.lndo.site or similar

The URL may be different depending on which ports are already take on your computer. Run `lando info` to see the actual URLs.
