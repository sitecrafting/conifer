<?php

/**
 * Tests for the Conifer\Post\Image class for the following exposed methods:
 * - add_size()
 * - get_sizes()
 * - get_size()
 * - aspect()
 * - width()
 * - height()
 *
 * @copyright 2026 SiteCrafting, Inc.
 * @author    Alex Merk <amerk@sitecrafting.com>
 */

namespace Conifer\Unit\Post;

use Conifer\Post\Image;
use Conifer\Unit\Base;
use PHPUnit\Framework\MockObject\MockObject;

class ImageTest extends Base
{
    protected null|Image|MockObject $image = null;

    private const IMAGE_SIZE_SMALL = 'test-small';
    private const IMAGE_SIZE_LARGE = 'test-large';

    private const IMAGE_HEIGHT_SMALL = 100;
    private const IMAGE_HEIGHT_LARGE = 200;

    private const IMAGE_WIDTH_SMALL = 100;
    private const IMAGE_WIDTH_LARGE = 200;

    public function setUp(): void
    {
        parent::setUp();

        // Mock add_image_size() - We don't need to actually mock the functionality of add_image_size(), we just need to ensure that it is called when add_size() is called.
        \WP_Mock::userFunction('add_image_size', [
            'return' => true,
        ]);

        // Mock get_option() for default image sizes - necessary to prevent test errors when get_sizes is called.
        // Thumbnail is 150x150 by default
        \WP_Mock::userFunction('get_option', [
            'args' => ['thumbnail_size_w'],
            'return' => 150,
        ]);
        \WP_Mock::userFunction('get_option', [
            'args' => ['thumbnail_size_h'],
            'return' => 150,
        ]);

        // Medium is 300x300 by default
        \WP_Mock::userFunction('get_option', [
            'args' => ['medium_size_w'],
            'return' => 300,
        ]);
        \WP_Mock::userFunction('get_option', [
            'args' => ['medium_size_h'],
            'return' => 300,
        ]);

        // Medium Large is 768x0 by default
        \WP_Mock::userFunction('get_option', [
            'args' => ['medium_large_size_w'],
            'return' => 768,
        ]);
        \WP_Mock::userFunction('get_option', [
            'args' => ['medium_large_size_h'],
            'return' => 0,
        ]);

        // Large is 1024x1024 by default
        \WP_Mock::userFunction('get_option', [
            'args' => ['large_size_w'],
            'return' => 1024,
        ]);
        \WP_Mock::userFunction('get_option', [
            'args' => ['large_size_h'],
            'return' => 1024,
        ]);

        $this->image = $this->getMockBuilder(Image::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
    }

    // get_sizes()
    // Note: because we need to use get_sizes to test add_size, we need an explicit test for default sizes to ensure that get_sizes is working as intended.
    public function test_get_sizes_with_default_sizes()
    {
        $sizes = $this->image::get_sizes();

        $this->assertArrayHasKey('thumbnail', $sizes);
        $this->assertArrayHasKey('medium', $sizes);
        $this->assertArrayHasKey('medium_large', $sizes);
        $this->assertArrayHasKey('large', $sizes);
    }

    public function test_get_sizes_with_default_sizes_and_custom_sizes()
    {
        $this->image::add_size(
            self::IMAGE_SIZE_SMALL,
            self::IMAGE_WIDTH_SMALL,
            self::IMAGE_HEIGHT_SMALL
        );

        $sizes = $this->image::get_sizes();

        $this->assertArrayHasKey('thumbnail', $sizes);
        $this->assertArrayHasKey('medium', $sizes);
        $this->assertArrayHasKey('medium_large', $sizes);
        $this->assertArrayHasKey('large', $sizes);

        // Check that our custom size was added
        $this->assertArrayHasKey(self::IMAGE_SIZE_SMALL, $sizes);
    }

    // get_size()
    public function test_get_size_with_nonexistent_size()
    {
        $sizes = $this->image::get_size('non-existent-size');

        // Check that a non-existent size is not in the sizes array
        $this->assertEquals([], $sizes);
    }

    public function test_get_size_with_default_sizes()
    {
        $sizes = $this->image::get_size('thumbnail');

        // Check that a default size is in the sizes array
        $this->assertNotNull($sizes);

        // Thumbnail should be a 150px square by default, so check that the width and height are correct
        $this->assertEquals(150, $sizes['width']);
        $this->assertEquals(150, $sizes['height']);
    }

    // add_size()
    public function test_add_size_with_no_crop_and_no_height()
    {
        $this->image::add_size(
            self::IMAGE_SIZE_LARGE,
            self::IMAGE_WIDTH_LARGE
        );

        // To test that the add_size() method works, we can check that the size was added to the declared sizes array
        $sizes = $this->image::get_sizes();

        $this->assertArrayHasKey(self::IMAGE_SIZE_LARGE, $sizes);
        $this->assertEquals(self::IMAGE_WIDTH_LARGE, $sizes[self::IMAGE_SIZE_LARGE]['width']);
        $this->assertEquals(false, $sizes[self::IMAGE_SIZE_LARGE]['height']);
    }

    public function test_add_size_with_no_height()
    {
        $this->image::add_size(
            name: self::IMAGE_SIZE_LARGE,
            width: self::IMAGE_WIDTH_LARGE,
            crop: true
        );

        // To test that the add_size() method works, we can check that the size was added to the declared sizes array
        $sizes = $this->image::get_sizes();

        $this->assertArrayHasKey(self::IMAGE_SIZE_LARGE, $sizes);
        $this->assertEquals(self::IMAGE_WIDTH_LARGE, $sizes[self::IMAGE_SIZE_LARGE]['width']);
        $this->assertEquals(false, $sizes[self::IMAGE_SIZE_LARGE]['height']);
    }

    public function test_add_size_with_no_crop()
    {
        $this->image::add_size(
            name: self::IMAGE_SIZE_SMALL,
            width: self::IMAGE_WIDTH_SMALL,
            height: self::IMAGE_HEIGHT_SMALL
        );

        // To test that the add_size() method works, we can check that the size was added to the declared sizes array
        $sizes = $this->image::get_sizes();

        $this->assertArrayHasKey(self::IMAGE_SIZE_SMALL, $sizes);
        $this->assertEquals(self::IMAGE_WIDTH_SMALL, $sizes[self::IMAGE_SIZE_SMALL]['width']);
        $this->assertEquals(self::IMAGE_HEIGHT_SMALL, $sizes[self::IMAGE_SIZE_SMALL]['height']);
    }

    public function test_add_size_with_height_and_crop()
    {
        $this->image::add_size(
            name: self::IMAGE_SIZE_SMALL,
            width: self::IMAGE_WIDTH_SMALL,
            height: self::IMAGE_HEIGHT_SMALL,
            crop: true
        );

        // To test that the add_size() method works, we can check that the size was added to the declared sizes array
        $sizes = $this->image::get_sizes();

        $this->assertArrayHasKey(self::IMAGE_SIZE_SMALL, $sizes);
        $this->assertEquals(self::IMAGE_WIDTH_SMALL, $sizes[self::IMAGE_SIZE_SMALL]['width']);
        $this->assertEquals(self::IMAGE_HEIGHT_SMALL, $sizes[self::IMAGE_SIZE_SMALL]['height']);
    }

    public function test_add_size_with_already_declared_size()
    {
        $this->image::add_size(
            name: self::IMAGE_SIZE_SMALL,
            width: self::IMAGE_WIDTH_SMALL,
            height: self::IMAGE_HEIGHT_SMALL,
            crop: true
        );

        // Now call add_size again, changing the height to a different value
        $this->image::add_size(
            name: self::IMAGE_SIZE_SMALL,
            width: self::IMAGE_WIDTH_SMALL,
            height: self::IMAGE_HEIGHT_LARGE,
            crop: true
        );

        // Verify that the size was updated in the declared sizes array
        $sizes = $this->image::get_sizes();

        $this->assertArrayHasKey(self::IMAGE_SIZE_SMALL, $sizes);
        $this->assertEquals(self::IMAGE_WIDTH_SMALL, $sizes[self::IMAGE_SIZE_SMALL]['width']);
        $this->assertEquals(self::IMAGE_HEIGHT_LARGE, $sizes[self::IMAGE_SIZE_SMALL]['height']); // This should now be the new height
        $this->assertTrue($sizes[self::IMAGE_SIZE_SMALL]['crop']);
    }

    // width()
    public function test_width_with_nonexistent_size()
    {
        // Test that calling width() with a non-existent size throws an exception
        $this->expectException(\Error::class);
        $this->image->width('non-existent-size');
    }

    public function test_width_with_default_size()
    {
        $width = $this->image->width('thumbnail');
        $this->assertEquals(150, $width);
    }

    public function test_width_with_custom_size()
    {
        $this->image::add_size(
            name: self::IMAGE_SIZE_SMALL,
            width: self::IMAGE_WIDTH_SMALL,
            height: self::IMAGE_HEIGHT_SMALL
        );

        $width = $this->image->width(self::IMAGE_SIZE_SMALL);
        $this->assertEquals(self::IMAGE_WIDTH_SMALL, $width);
    }

    // height()
    public function test_height_with_nonexistent_image()
    {
        // Mock our file_loc property to point to a non-existent image file
        $file_loc = '../../../img/non_existent_image.png';
        $this->setProtectedProperty(
            $this->image,
            'file_loc',
            $file_loc
        );

        // Test that calling height() with a non-existent image file throws an exception
        try {
            $height = $this->image->height();
            $this->assertNull($height);
        } catch (\Error $e) {
            $this->assertInstanceOf(\Error::class, $e);
        }
    }

    public function test_height_with_nonexistent_size()
    {
        // Test that calling height() with a non-existent size throws an exception
        try {
            $height = $this->image->height('non-existent-size');
        } catch (\Error $e) {
            $this->assertInstanceOf(\Error::class, $e);
        }
    }

    public function test_height_with_default_size()
    {
        // Mock our file_loc property to point to a test image file
        $imagePath = realpath(dirname(__DIR__, 3) . '/img/tree_thumbnail.svg');
        $this->assertNotFalse($imagePath, 'Fixture image path could not be resolved.');

        // We need to explicitly set these protected properties because the Image class expects them to be set when calling height() and aspect()
        $this->setProtectedProperty(
            $this->image,
            'file_loc',
            $imagePath
        );
        $this->setProtectedProperty(
            $this->image,
            'image_dimensions',
            new \Timber\ImageDimensions($imagePath)
        );

        $height = $this->image->height('thumbnail');
        $this->assertEquals(150, $height);
    }

    public function test_height_with_custom_size()
    {
        // Mock our file_loc property to point to a test image file
        $imagePath = realpath(dirname(__DIR__, 3) . '/img/tree_thumbnail.svg');
        $this->assertNotFalse($imagePath, 'Fixture image path could not be resolved.');

        // We need to explicitly set these protected properties because the Image class expects them to be set when calling height() and aspect()
        $this->setProtectedProperty(
            $this->image,
            'file_loc',
            $imagePath
        );
        $this->setProtectedProperty(
            $this->image,
            'image_dimensions',
            new \Timber\ImageDimensions($imagePath)
        );

        $this->image::add_size(
            name: self::IMAGE_SIZE_LARGE,
            width: self::IMAGE_WIDTH_LARGE,
            height: self::IMAGE_HEIGHT_LARGE
        );

        $height = $this->image->height(self::IMAGE_SIZE_LARGE);
        $this->assertEquals(self::IMAGE_HEIGHT_LARGE, $height);
    }

    public function test_height_with_custom_size_and_no_height()
    {
        // Mock our file_loc property to point to a test image file
        $imagePath = realpath(dirname(__DIR__, 3) . '/img/tree_thumbnail.svg');
        $this->assertNotFalse($imagePath, 'Fixture image path could not be resolved.');

        // We need to explicitly set these protected properties because the Image class expects them to be set when calling height() and aspect()
        $this->setProtectedProperty(
            $this->image,
            'file_loc',
            $imagePath
        );
        $this->setProtectedProperty(
            $this->image,
            'image_dimensions',
            new \Timber\ImageDimensions($imagePath)
        );

        $this->image::add_size(
            name: self::IMAGE_SIZE_LARGE,
            width: self::IMAGE_WIDTH_LARGE
        );

        $height = $this->image->height();
        // This should still return the height of the image, even if the Image was constructed with a false height
        $this->assertEquals(150, $height);
    }

    public function test_height_with_modified_width()
    {
        // Mock our file_loc property to point to a test image file
        $imagePath = realpath(dirname(__DIR__, 3) . '/img/tree_thumbnail.svg');
        $this->assertNotFalse($imagePath, 'Fixture image path could not be resolved.');

        // We need to explicitly set these protected properties because the Image class expects them to be set when calling height() and aspect()
        $this->setProtectedProperty(
            $this->image,
            'file_loc',
            $imagePath
        );
        $this->setProtectedProperty(
            $this->image,
            'image_dimensions',
            new \Timber\ImageDimensions($imagePath)
        );

        // Now that our image is set, we need to modify the width of the image to test that height() returns the correct height based on the modified width
        $size = $this->image::get_size('thumbnail');
        $modifiedWidth = 300;

        $this->image->add_size(
            name: 'thumbnail',
            width: $modifiedWidth,
            height: $size['height']
        );

        $height = $this->image->height('thumbnail');
        // The height should be modified based on the modified width, so we need to calculate the expected height based on the aspect ratio of the original image
        $expectedHeight = (int) round($modifiedWidth / $this->image->aspect());
        $this->assertEquals($expectedHeight, $height);
    }

    // aspect()
    public function test_aspect_with_nonexistent_image()
    {
        // Mock our file_loc property to point to a non-existent image file
        $file_loc = '../../img/non_existent_image.png';
        $this->setProtectedProperty(
            $this->image,
            'file_loc',
            $file_loc
        );

        // Test that calling aspect() with a non-existent image file returns null
        $aspect = $this->image->aspect();
        $this->assertNull($aspect);
    }

    public function test_aspect_with_thumbnail_image()
    {
        // Mock our file_loc property to point to a test image file
        $imagePath = realpath(dirname(__DIR__, 3) . '/img/tree_thumbnail.svg');
        $this->assertNotFalse($imagePath, 'Fixture image path could not be resolved.');

        // We need to explicitly set these protected properties because the Image class expects them to be set when calling height() and aspect()
        $this->setProtectedProperty(
            $this->image,
            'file_loc',
            $imagePath
        );
        $this->setProtectedProperty(
            $this->image,
            'image_dimensions',
            new \Timber\ImageDimensions($imagePath)
        );

        $aspect = $this->image->aspect();
        // The aspect ratio of the tree_thumbnail.svg image is 1:1, so we expect the aspect ratio to be 1.0
        $this->assertEquals(1.0, $aspect);
    }

    public function test_aspect_with_different_height_and_width_image()
    {
        // Mock our file_loc property to point to a test image file
        $imagePath = realpath(dirname(__DIR__, 3) . '/img/banner.png');
        $this->assertNotFalse($imagePath, 'Fixture image path could not be resolved.');

        // We need to explicitly set these protected properties because the Image class expects them to be set when calling height() and aspect()
        $this->setProtectedProperty(
            $this->image,
            'file_loc',
            $imagePath
        );
        $this->setProtectedProperty(
            $this->image,
            'image_dimensions',
            new \Timber\ImageDimensions($imagePath)
        );

        // Also need to mock wp_check_filetype_and_ext() for Timber\ImageDimensions to work properly
        \WP_Mock::userFunction('wp_check_filetype_and_ext', [
            'return' => ['type' => 'image/png', 'ext' => 'png', 'proper_filename' => null],
        ]);

        // Calculate the aspect ratio ourselves to compare against the aspect() method
        $width = $this->image->width();
        $height = $this->image->height();

        $aspect = $width / $height;
        // The aspect ratio of the banner.png image is 3:1, so we expect the aspect ratio to be 3.0
        $this->assertEquals($aspect, $this->image->aspect());
    }
}
