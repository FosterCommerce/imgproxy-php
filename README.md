# imgproxy-php

A PHP library for working with imgproxy. This package provides a simple way to build URLs with imgproxy processing options.

## Installation

```bash
composer require fostercommerce/imgproxy-php
```

## Usage

### Creating Processing Options

```php
use fostercommerce\imgproxy\Options;

// Create a new Options object
$options = new Options();

// Set options with individual setters
$options->setWidth(300)
    ->setHeight(400)
    ->setResizingType('fill')
    ->setGravity('sm');

// Get string representation for URL
echo $options->toString();
// Output: "width:300/height:400/resizing_type:fill/gravity:sm"

// Options object can also be used directly in a string context
echo $options;
// Output: "width:300/height:400/resizing_type:fill/gravity:sm"
```

### Alternative Initialization

It is possible to initialize the Options object with an associative array. The keys of the array are the option names and the values are the option values.

The values can either be a single value, an array of values, or an associative array of values.

- When a single value is used, it is passed as the first argument to the setter method.
- When an array of values is used, they are destructured as passed as arguments in order to the setter method.
- When an associative array is used, the key/value pairs are destructured and passed in as named arguments to the setter method.

```php
// Create Options object with array of values
$options = new Options([
    'width' => 300,
    'height' => 400,
    'resize' => [
        'width' => 300,
        'height' => 400,
        'enlarge' => false,
        'resize_type' => 'fill',
    ],
    'gravity' => 'sm',
    'png_options' => [
        'interlaced' => true,
        'quantize' => false,
    ],
]);
```

### Building URLs

```php
use fostercommerce\imgproxy\Options;
use fostercommerce\imgproxy\UrlBuilder;

// Create options object
$options = new Options();
$options->setPreset('sharp')
    ->setResize('fill', 300, 400, false)
    // or
    ->setResize([
        'width' => 300,
        'height' => 400,
        'enlarge' => false,
        'resize_type' => 'fill',
    ])
    ->setGravity('sm')
    ->setQuality(80)
    ->setFormat('png');

// Create URL builder (without signing, with plain URLs)
$builder = new UrlBuilder('https://imgproxy.example.com', null, null, false);

// Build URL
$imageUrl = 'https://example.com/images/image.jpg';
$url = $builder->buildUrl($imageUrl, $options);

echo $url;
// Output: https://imgproxy.example.com/unsafe/preset:sharp/resize:fill:300:400:0/gravity:sm/quality:80/format:png/plain/https://example.com/images/image.jpg
```

### Encoded URLs

```php
use fostercommerce\imgproxy\Options;
use fostercommerce\imgproxy\UrlBuilder;

// Create options object
$options = new Options();
$options->setWidth(300)
    ->setHeight(400)
    ->setResizingType('fill');

// Create URL builder with base64 encoding (default behavior)
$builder = new UrlBuilder('https://imgproxy.example.com', null, null, true);

// Build URL with encoded source
$imageUrl = 'https://example.com/images/image.jpg';
$url = $builder->buildUrl($imageUrl, $options);

echo $url;
// Output: https://imgproxy.example.com/unsafe/width:300/height:400/resizing_type:fill/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZXMvaW1hZ2UuanBn

// With extension
$url = $builder->buildUrl($imageUrl, $options, 'png');
echo $url;
// Output: https://imgproxy.example.com/unsafe/width:300/height:400/resizing_type:fill/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZXMvaW1hZ2UuanBn.png
```

### Signed URLs

```php
// Create URL builder with signing keys (using plain URLs)
$key = '0123456789abcdef0123456789abcdef';
$salt = 'fedcba9876543210fedcba9876543210';
$builder = new UrlBuilder('https://imgproxy.example.com', $key, $salt, false);

// Build signed URL
$imageUrl = 'https://example.com/images/image.jpg';
$url = $builder->buildUrl($imageUrl, $options, 'png');

echo $url;
// Output will include a signature: https://imgproxy.example.com/[signature]/preset:sharp/resize:fill:300:400:0/gravity:sm/quality:80/format:png/plain/https://example.com/images/image.jpg@png
```

### Signed and Encoded URLs

```php
// Create URL builder with signing keys and base64 encoding
$key = '0123456789abcdef0123456789abcdef';
$salt = 'fedcba9876543210fedcba9876543210';
$builder = new UrlBuilder('https://imgproxy.example.com', $key, $salt, true);

// Build signed URL with encoded source
$imageUrl = 'https://example.com/images/image.jpg';
$url = $builder->buildUrl($imageUrl, $options);

echo $url;
// Output will include a signature: https://imgproxy.example.com/[signature]/preset:sharp/resize:fill:300:400:0/gravity:sm/quality:80/format:png/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZXMvaW1hZ2UuanBn
```

## Development

### Testing

This package uses [Pest PHP](https://pestphp.com/) for testing. To run the tests:

```bash
composer test
```

or

```bash
vendor/bin/pest
```