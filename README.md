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

```php
// Create Options object with array of values
$options = new Options([
    'width' => 300,
    'height' => 400,
    'resizingType' => 'fill',
    'gravity' => 'sm',
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