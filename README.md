# Conifer

> #### Warning::ALPHA STATUS

> Conifer is in Alpha. We consider the code production-ready, and breaking changes to the API are unlikely. Most of the code has been extracted from components already running in production.
>
> **However, there may be lingering bugs and, if necessary, breaking changes at this early stage.**

[![Powerful abstractions on top of Timber for simple, opinionated OO WordPress development.](https://raw.githubusercontent.com/sitecrafting/conifer/master/img/banner-green.png)](https://coniferplug.in)

[![Build Status](https://travis-ci.org/sitecrafting/conifer.svg?branch=timber-2.x)](https://travis-ci.org/sitecrafting/conifer)
[![Build Status](https://img.shields.io/packagist/v/sitecrafting/conifer.svg)](https://packagist.org/packages/sitecrafting/conifer)

## Documentation

For reference documentation, use-cases, and design principles, [check out the docs](https://www.coniferplug.in/).

## Quick start

See the [Installation docs](https://www.coniferplug.in/installation.html) to get Conifer installed for use in your theme or plugin.

## Issues and Feature Requests

Please submit issues and feature requests directly to GitHub. We need guidance from the community about how people would like to use Conifer!

## Development

**To get started hacking on Conifer itself, first install [Lando](https://docs.devwithlando.io/),** the official dev environment for Conifer and sole system requirement for developing Conifer. **Note that you need Lando version beta.47 or rc.1.** The transition to rc.2+ is a [work in progress](https://github.com/sitecrafting/conifer/issues/103).

Then, just clone the repo and start up the dev environment:

```
git clone git@github.com:sitecrafting/conifer.git
cd conifer
lando start
```

Follow the prompts and you should have a local WordPress site running Conifer
and its companion starter theme, [Groot](https://github.com/sitecrafting/groot)!

**NOTE: there is currently a known issue with how Lando sets up pretty permalinks. They won't work before you configure them manually.**

## Authored by SiteCrafting

Built with 💚 + ☕ in Tacoma, Washington.

[![Work with SiteCrafting on your next web project](https://raw.githubusercontent.com/sitecrafting/conifer/master/img/sc-banner.jpg)](https://www.sitecrafting.com/)

### Building a new release

Groot includes a script for building itself and creating a release. To create a tag and corresponding release called `vX.Y.Z`:

```bash
scripts/build-release.sh vX.Y.Z
```

This will create a .tar.gz and a .zip archive which you can upload to a new release on GitHub.

If you have [`hub`](https://hub.github.com/) installed, it will also prompt you to optionally create a release directly!
