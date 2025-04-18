<?php

use fostercommerce\imgproxy\Options;

// Test creating Options with individual setters
test('can set options with individual setters', function () {
    $options = createOptions();
    $options->setWidth(300)
        ->setHeight(400)
        ->setResizingType('fill')
        ->setGravity('sm');

    expect($options)->toBeOptions();
    expect($options->toString())->toBe('width:300/height:400/resizing_type:fill/gravity:sm');
});

// Test creating Options with constructor array
test('can set options with constructor array', function () {
    $options = createOptions([
        'width' => 300,
        'height' => 400,
        'resizingType' => 'fill',
        'gravity' => 'sm',
    ]);

    expect($options)->toBeOptions();
    expect($options->toString())->toBe('width:300/height:400/resizing_type:fill/gravity:sm');
});

// Test complex options
test('can handle complex options', function () {
    $options = createOptions();
    $options->setPreset('sharp')
        ->setResize('fill', 300, 400, false)
        ->setGravity('sm')
        ->setWatermark(0.5, 'ce', 10, 10, 0.2)
        ->setQuality(80)
        ->setFormat('png');

    expect($options)->toBeOptions();
    expect($options->toString())->toBe('preset:sharp/resize:fill:300:400:0/gravity:sm/watermark:0.5:ce:10:10:0.2/quality:80/format:png');
});

// Test toString method and __toString magic method
test('magic __toString behaves like toString method', function () {
    $options = createOptions();
    $options->setPreset('sharp')
        ->setResize('fill', 300, 400, false);

    expect($options->toString())->toBe((string) $options);
});

// Test boolean value conversion
test('boolean values are converted to 1 and 0', function () {
    $options = createOptions();
    $options->setEnlarge(true)
        ->setAutoRotate(false)
        ->setStripMetadata(true);

    expect($options->toString())->toBe('enlarge:1/auto_rotate:0/strip_metadata:1');
});
