
### Class: \Conifer\Post\Image

> Custom Image class for maintaining image sizes. Image sizes/dimensions are more easily retrievable when declared via this class.

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>add_size(</strong><em>string</em> <strong>$name</strong>, <em>int</em> <strong>$width</strong>, <em>bool/int</em> <strong>$height=false</strong>, <em>bool/boolean</em> <strong>$crop=false</strong>)</strong> : <em>void</em><br /><em>Thin wrapper around add_image_size(). Remembers arguments so that the newly declared size can be looked up later using get_sizes().</em> |
| public | <strong>aspect()</strong> : <em>mixed image aspect ratio as a float, or null if the image does not exist</em><br /><em>Get the aspect ratio of the underlying image file.</em> |
| public static | <strong>get_size(</strong><em>array</em> <strong>$size</strong>)</strong> : <em>array</em><br /><em>Get dimension info for the custom image size $size</em> |
| public static | <strong>get_sizes()</strong> : <em>array</em><br /><em>Get all images sizes, including default ones</em> |
| public | <strong>height(</strong><em>bool/string</em> <strong>$customSize=false</strong>)</strong> : <em>int</em><br /><em>Get the declared height of this image, optionally specific to the image size $size</em> |
| public | <strong>width(</strong><em>bool/string</em> <strong>$customSize=false</strong>)</strong> : <em>int</em><br /><em>Get the declared width of this image, optionally specific to the image size $size</em> |

*This class extends \Timber\Image*

*This class implements \Timber\CoreInterface*

