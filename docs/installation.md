# Installation

## Installing with Composer

This is the recommended route for most use-cases.

```
composer require --save sitecrafting/conifer
```

In order to put Conifer in the wp-content/plugins directory
automatically, we recommend [composer-custom-directory-installer](https://github.com/mnsami/composer-custom-directory-installer):

```json
{
  "require": {
    "sitecrafting/conifer": "dev-master"
  },
  "require-dev": {
    "mnsami/composer-custom-directory-installer": "^1.1"
  }
}
```

## Installing from an official release

1. Go to https://github.com/sitecrafting/conifer/releases
2. Download a zip or tar
3. Place the unarchived directory inside `wp-content/plugins/`

## From source

```
git clone https://github.com/sitecrafting/conifer /path/to/wp-content/plugins/conifer
cd /path/to/wp-content/plugins/conifer
composer install
```

**Note:** If you are installing in production, you can safely specify the `--no-dev` argument to `composer`. **If you use this process, Conifer assumes you have set up your [system requirements](/requirements.md) manually.** Running `composer install --no-dev` only creates the Conifer library autoloader, and nothing else.