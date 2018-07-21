# Development Setup

[Lando](https://docs.devwithlando.io) is the official Conifer development environment, and comes highly recommended. It's the best local dev tool in the galaxy!

We also support [Varying Vagrant Vagrants](https://varyingvagrantvagrants.org/), although much of the tooling around testing etc. may require some tweaking depending on your setup.

## Using Lando (Recommended)

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

The first time you start the app, it will install WordPress, Timber, and the Groot starter theme for you. It will prompt you for various admin setup details, similar to the WP installation process. Then it will expose your local Conifer dev site at https://conifer.lndo.site (or similiar).

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
* `lando e2e`: run Conifer's end-to-end Cypress test suite
* `lando cypress`: run arbitrary `cypress` commands
* `lando sniff`: run [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer) on the Conifer codebase
* `lando debug`: `tail` the WP debug.log in real time
* `lando yarn`: run arbitrary [yarn](https://www.npmjs.com/package/yarn) commands
* `lando newman`: Run arbitrary [newman](https://www.npmjs.com/package/newman) commands. Not currently in use.

#### Other development tools

Conifer's Lando environment also includes:

* a [phpMyAdmin](https://www.phpmyadmin.net/) installation at https://phpmyadmin.conifer.lndo.site or similar
* a [Mailhog](https://github.com/mailhog/MailHog) instance for catching and managing outgoing mail at https://mailhog.conifer.lndo.site or similar

These URLs may be different depending on which ports are already take on your computer. Tun `lando info` to see the actual URLs.

## Using Varying Vagrant Vagrants (VVV)

**NOTE: We are happy to support Vagrant as a secondary/backup environment if for some reason you can't run Lando/Docker on your machine. However, please do us a favor and try the Lando setup first. It is drastically simpler to maintain and the tooling is much more tightly integrated.**

### Pre-requisite: setup VVV

You will obviously want to have VVV configured before you start. If you run into trouble with this, check your system against the [VVV requirements](https://varyingvagrantvagrants.org/docs/en-US/installation/software-requirements/), or take a look at the [troubleshooting guide](https://varyingvagrantvagrants.org/docs/en-US/troubleshooting/).

**Non-standard VVV setups are not supported. GitHub issues about custom VVV configurations will be closed immediately. You have been warned.**

### Add the `xvfb` core utility

[Cypress](https://www.cypress.io/), the CLI tool used for Conifer end-to-end tests, requires [xvfb](https://www.x.org/releases/X11R7.7/doc/man/man1/Xvfb.1.xhtml) for running its headless browser. Tell VVV to install it by adding it as a core utility in `vvv-custom.yml`:

```yaml
# the core utilities install tools such as phpmyadmin
utilities:
  core:
    # ...existing utils...
    - xvfb
```

### Provision the dev site

Add this to your `sites` block in `vvv-custom.yml`:

```yaml
  conifer:
    repo: https://github.com/sitecrafting/conifer.git
      - conifer.wordpress.test
```

Run `vagrant reload --provision` per usual.

The provisioning scripts within Conifer will install composer dependencies.

### Install front-end tooling

Front-end tooling for VVV is a work in progress and is currently **low-priority**. For now, you can run:

```bash
sudo npm install -g yarn newman
```

