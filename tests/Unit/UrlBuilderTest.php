<?php

use fostercommerce\imgproxy\Options;
use fostercommerce\imgproxy\UrlBuilder;

// Test basic URL building
test('can build basic URL', function () {
    $options = createOptions([
        'width' => 300,
        'height' => 400,
        'resizingType' => 'fill',
        'gravity' => 'sm',
    ]);

    $builder = createUrlBuilder();
    $url = $builder->buildUrl('https://example.com/images/image.jpg', $options);

    expect($builder)->toBeUrlBuilder();
    expect($url)->toBe('https://imgproxy.example.com/unsafe/width:300/height:400/resizing_type:fill/gravity:sm/plain/https://example.com/images/image.jpg');
});

// Test URL with extension
test('can build URL with extension', function () {
    $options = createOptions([
        'width' => 300,
        'height' => 400,
    ]);

    $builder = createUrlBuilder();
    $url = $builder->buildUrl('https://example.com/images/image.jpg', $options, 'png');

    expect($url)->toBe('https://imgproxy.example.com/unsafe/width:300/height:400/plain/https://example.com/images/image.jpg@png');
});

// Test URL with special characters
test('can handle URLs with query strings', function () {
    $options = createOptions([
        'width' => 300,
    ]);

    $builder = createUrlBuilder();
    $url = $builder->buildUrl('https://example.com/images/image.jpg?width=100', $options);

    expect($url)->toBe('https://imgproxy.example.com/unsafe/width:300/plain/https://example.com/images/image.jpg%3Fwidth=100');
});

// Test URL with @ symbol
test('can handle URLs with @ symbol', function () {
    $options = createOptions([
        'width' => 300,
    ]);

    $builder = createUrlBuilder();
    $url = $builder->buildUrl('https://example.com/images/user@example.jpg', $options);

    expect($url)->toBe('https://imgproxy.example.com/unsafe/width:300/plain/https://example.com/images/user%40example.jpg');
});

// Test signed URL
test('can generate signed URLs', function () {
    $options = createOptions([
        'width' => 300,
        'height' => 400,
    ]);

    $key = '0123456789abcdef0123456789abcdef';
    $salt = 'fedcba9876543210fedcba9876543210';

    $builder = createUrlBuilder('https://imgproxy.example.com', $key, $salt);
    $url = $builder->buildUrl('https://example.com/images/image.jpg', $options);

    // Use the custom matcher to validate the URL structure with signature
    expect($url)->toMatchSignedImgproxyUrl(
        'https://imgproxy.example.com',
        'width:300/height:400',
        'https://example.com/images/image.jpg'
    );

    // Also verify that it's not using the "unsafe" signature
    expect($url)->not->toContain('/unsafe/');

    // Test with extension
    $url = $builder->buildUrl('https://example.com/images/image.jpg', $options, 'png');

    expect($url)->toMatchSignedImgproxyUrl(
        'https://imgproxy.example.com',
        'width:300/height:400',
        'https://example.com/images/image.jpg@png'
    );
});

// Test signed URL with complex options
test('can generate complex signed URLs', function () {
    $options = createOptions();
    $options->setPreset('sharp')
        ->setResize('fill', 300, 400, false)
        ->setGravity('sm')
        ->setWatermark(0.5, 'ce', 10, 10, 0.2)
        ->setQuality(80)
        ->setFormat('png');

    $key = '0123456789abcdef0123456789abcdef';
    $salt = 'fedcba9876543210fedcba9876543210';

    $builder = createUrlBuilder('https://imgproxy.example.com', $key, $salt);
    $url = $builder->buildUrl('https://example.com/images/image.jpg', $options);

    // Use the custom matcher to validate the complex URL structure with signature
    expect($url)->toMatchSignedImgproxyUrl(
        'https://imgproxy.example.com',
        'preset:sharp/resize:fill:300:400:0/gravity:sm/watermark:0.5:ce:10:10:0.2/quality:80/format:png',
        'https://example.com/images/image.jpg'
    );

    expect($url)->not->toContain('/unsafe/');
});

// Test URL with complex options
test('can build URL with complex options', function () {
    $options = createOptions();
    $options->setPreset('sharp')
        ->setResize('fill', 300, 400, false)
        ->setGravity('sm')
        ->setWatermark(0.5, 'ce', 10, 10, 0.2)
        ->setQuality(80)
        ->setFormat('png');

    $builder = createUrlBuilder();
    $url = $builder->buildUrl('https://example.com/images/image.jpg', $options);

    expect($url)->toBe('https://imgproxy.example.com/unsafe/preset:sharp/resize:fill:300:400:0/gravity:sm/watermark:0.5:ce:10:10:0.2/quality:80/format:png/plain/https://example.com/images/image.jpg');
});

// Test base64 encoded URL
test('can build base64 encoded URL', function () {
    $options = createOptions([
        'width' => 300,
        'height' => 400,
    ]);

    $builder = createUrlBuilder('https://imgproxy.example.com', null, null, true);
    $url = $builder->buildUrl('https://example.com/images/image.jpg', $options);

    // Calculate the expected base64 encoded URL
    $encodedUrl = rtrim(strtr(base64_encode('https://example.com/images/image.jpg'), '+/', '-_'), '=');

    expect($url)->toBe("https://imgproxy.example.com/unsafe/width:300/height:400/{$encodedUrl}");
});

// Test base64 encoded URL with extension
test('can build base64 encoded URL with extension', function () {
    $options = createOptions([
        'width' => 300,
        'height' => 400,
    ]);

    $builder = createUrlBuilder('https://imgproxy.example.com', null, null, true);
    $url = $builder->buildUrl('https://example.com/images/image.jpg', $options, 'png');

    // Calculate the expected base64 encoded URL
    $encodedUrl = rtrim(strtr(base64_encode('https://example.com/images/image.jpg'), '+/', '-_'), '=');

    expect($url)->toBe("https://imgproxy.example.com/unsafe/width:300/height:400/{$encodedUrl}.png");
});

// Test base64 encoded URL with special characters
test('can handle base64 encoded URLs with special characters', function () {
    $options = createOptions([
        'width' => 300,
    ]);

    $builder = createUrlBuilder('https://imgproxy.example.com', null, null, true);

    // Test URL with query string
    $url1 = $builder->buildUrl('https://example.com/images/image.jpg?width=100', $options);

    // Test URL with @ symbol
    $url2 = $builder->buildUrl('https://example.com/images/user@example.jpg', $options);

    // Calculate the expected base64 encoded URLs
    $encodedUrl1 = rtrim(strtr(base64_encode('https://example.com/images/image.jpg?width=100'), '+/', '-_'), '=');
    $encodedUrl2 = rtrim(strtr(base64_encode('https://example.com/images/user@example.jpg'), '+/', '-_'), '=');

    expect($url1)->toBe("https://imgproxy.example.com/unsafe/width:300/{$encodedUrl1}");
    expect($url2)->toBe("https://imgproxy.example.com/unsafe/width:300/{$encodedUrl2}");
});

// Test signed base64 encoded URL
test('can generate signed base64 encoded URLs', function () {
    $options = createOptions([
        'width' => 300,
        'height' => 400,
    ]);

    $key = '0123456789abcdef0123456789abcdef';
    $salt = 'fedcba9876543210fedcba9876543210';

    $builder = createUrlBuilder('https://imgproxy.example.com', $key, $salt, true);
    $url = $builder->buildUrl('https://example.com/images/image.jpg', $options);

    // Use the custom matcher to validate the base64 encoded URL structure with signature
    expect($url)->toMatchSignedBase64ImgproxyUrl(
        'https://imgproxy.example.com',
        'width:300/height:400',
        'https://example.com/images/image.jpg'
    );

    // Also verify that it's not using the "unsafe" signature
    expect($url)->not->toContain('/unsafe/');

    // Test with extension
    $url = $builder->buildUrl('https://example.com/images/image.jpg', $options, 'png');

    expect($url)->toMatchSignedBase64ImgproxyUrl(
        'https://imgproxy.example.com',
        'width:300/height:400',
        'https://example.com/images/image.jpg',
        'png'
    );
});

// Test signed base64 encoded URL with complex options
test('can generate complex signed base64 encoded URLs', function () {
    $options = createOptions();
    $options->setPreset('sharp')
        ->setResize('fill', 300, 400, false)
        ->setGravity('sm')
        ->setWatermark(0.5, 'ce', 10, 10, 0.2)
        ->setQuality(80)
        ->setFormat('png');

    $key = '0123456789abcdef0123456789abcdef';
    $salt = 'fedcba9876543210fedcba9876543210';

    $builder = createUrlBuilder('https://imgproxy.example.com', $key, $salt, true);
    $url = $builder->buildUrl('https://example.com/images/image.jpg', $options);

    // Use the custom matcher to validate the complex URL structure with signature
    expect($url)->toMatchSignedBase64ImgproxyUrl(
        'https://imgproxy.example.com',
        'preset:sharp/resize:fill:300:400:0/gravity:sm/watermark:0.5:ce:10:10:0.2/quality:80/format:png',
        'https://example.com/images/image.jpg'
    );

    expect($url)->not->toContain('/unsafe/');
});
