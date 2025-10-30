# How to Contribute to Conifer

Thank you for your interest in contributing to Conifer. We'd love your help! 😃

Keep in mind that coding is just one of many ways you can help. We welcome people of all kinds of abilities who want to contribute to making WordPress development easier. Here are some things you can do:

* Create instructional videos
* Write examples and documentation
* [Report issues](https://github.com/sitecrafting/conifer/issues) or commenting on existing issues: **we want to hear from you!**
* [Send us a message](mailto:team@coniferplug.in) 👋

Before reporting an issue, please be sure to check over the [existing issues](https://github.com/sitecrafting/conifer/issues) on GitHub.

When reporting a new issue, as a courtesy to your humble authors and to help us help you, please follow the [issue template](https://github.com/sitecrafting/conifer/issues/new).

## Bug Fixes and New Features

Of course, new contributions in the form of code are welcome too, *especially* while Conifer is in its early days! 👶 To do that, just head over to GitHub and start a discussion in a new issue, or create a Pull Request.

**Before making a pull request, please familiarize yourself with the [Testing docs](/testing.md).** It's a good idea to also be aware of the [Code of Conduct](/code-of-conduct.md) (TL;DR: don't be a jerk).

If you're wondering how best to set up Conifer to work on the source code, check out [Development Setup](/dev-setup.md).

## Pull Request Guidelines

In general, please follow the Pull Request template that GitHub prompts you with when you create a PR. This comprises various criteria you should think through. Not all criteria necessarily need to be met for every PR (for example, update to documentation only don't need unit tests). Please apply good judgment, think through the effects of your change, and try to empathize with maintainers as well as future developers who may be interested in the reasoning behind a given change.

If you edit any code, please run unit tests and coding standard checks (`lando unit`, `lando sniff`, and `lando rector`, respectively) before creating your PR. If either of these checks fails, it is your responsibility to figure out why, and fix any failures caused by your code. Of course, if you don't understand why something failed or the reasoning behind a certain test/check, feel free to reach out and we can work with you to figure out the best solution!
