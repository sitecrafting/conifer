# Images

Conifer extends Timber's [Image](https://timber.github.io/docs/v2/reference/timber-image/) class with helpers for managing registered image sizes and reading image dimensions.

## Image Sizes

### Default Image Sizes

Conifer includes the standard WordPress size names by default:

- `thumbnail`
- `medium`
- `medium_large`
- `large`

Conifer reads each size's dimensions from WordPress options (for example, `thumbnail_size_w` and `thumbnail_size_h`), so the actual values reflect your site's Media settings.

### Getting Image Sizes

Use `get_sizes()` to fetch all configured sizes, or `get_size()` to fetch a single size by name.

```php
<?php

declare(strict_types=1);

use Conifer\Post\Image;

// Get all configured sizes.
$sizes = Image::get_sizes();

// Get one configured size.
$size = Image::get_size('medium');
```

### Adding a New Image Size

Use Conifer's wrapper around `add_image_size()` to register custom sizes.

```php

<?php

use Conifer\Post\Image;
use Conifer\Site;

$site = new Site();
$site->configure(function () {
    Image::add_size('home-hero', 1440, 790, true);
    Image::add_size('interior-hero', 1440, 400, true);
    Image::add_size('interior-fullbleed', 1440, 530, true);
});
```

## Image dimensions

Use `height()`, `width()`, and `aspect()` on a Conifer image instance.

```php
<?php

declare(strict_types=1);

use Timber\Timber;

$context = Timber::context();

$cover_image_id = $context['post']->cover_image;
$image = Timber::get_image($cover_image_id);

if ($image) {
  // Original image dimensions.
  $height = $image->height();
  $width = $image->width();

  // Dimensions for a specific registered size.
  $custom_height = $image->height('home-hero');
  $custom_width = $image->width('home-hero');

  // Underlying file aspect ratio.
  $aspect = $image->aspect();
}
```

You can also use these methods directly in Twig templates:

```twig
<img
  src="{{ image.src }}"
  height="{{ image.height }}"
  width="{{ image.width }}"
/>
```
