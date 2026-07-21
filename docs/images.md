# Images

Conifer extends the functionality of the [Timber2 Image](https://timber.github.io/docs/v2/reference/timber-image/) class in a few ways. It includes default Image sizes, wrapper functions for getting configured Image sizes, and wrapper functions for getting the height/width/aspect of an Image.

## Image Sizes

### Default Image sizes

Conifer includes the following image sizes by default
  - `thumbnail`
    - width: 150px
    - height: 150px
  - `medium`    
    - width: 300px
    - height: 300px
  - `medium_large`
    - width: 768px
    - height: 768px
  - `large`
    - width: 1024px
    - height: 1024px

### Getting Image sizes

Conifer includes two functions for fetching image sizes, `get_sizes` and `get_size`

```php
<?php

declare(strict_types=1);

use Conifer\Post\Image;

// Get all of the configured sizes
$sizes = Image::get_sizes();

// Get a single configured size
$size = Image::get_size('medium');
```

### Adding a new Image size

Conifer includes a wrapper around `add_image_size()` to allow you to easily add new sizes for Images

```php
<?php

declare(strict_types=1);

namespace Project;

use Conifer\Post\Image;
use Conifer\Site as ConiferSite;

class Site extends ConiferSite
{
    public function configure(?callable $userDefinedConfig = null, bool $configureDefaults = true): ConiferSite
	{
        //...

        // Add custom image sizes
        Image::add_size('home-hero', 1440, 790, true);
		Image::add_size('interior-hero', 1440, 400, true);
		Image::add_size('interior-fullbleed', 1440, 530, true);
        // etc..
    }
}
```

## Image dimensions

When working with Images, Conifer includes some utility functions to get the dimensions of your image: `height()`, `width()`, and `aspect()`

```php
<?php

declare(strict_types=1);

use Conifer\Post\Image;

private Image $image;

$context = Timber::context();

$cover_image_id = $context['post']->cover_image;
$image = Timber::get_post($cover_image_id);

// Get the height of an Image
$height = $image->height();

// Or, if your Image is using a custom size
$height = $image->height( true );

// Get the width of an Image
$width = $image->width();

// Or, if your Image is using a custom size
$width = $image->width( true );

// Get the aspect ratio for an underlying image
$aspect = $image->aspect();
```

You can also use the same functions in Twig templates

```twig
<img 
    src="{{ image.src }}" 
    height="{{ image.height }}" 
    width="{{ image.width }}"
/>
```
