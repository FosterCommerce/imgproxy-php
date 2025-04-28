<?php


test('can set options with individual setters', function (): void {
	$options = createOptions();
	$options->setWidth(300)
		->setHeight(400)
		->setResizingType('fill')
		->setGravity('sm');

	expect($options)->toBeOptions();
	expect($options->toString())->toBe('width:300/height:400/resizing_type:fill/gravity:sm');
});

test('can set options with constructor array', function (): void {
	$options = createOptions([
		'width' => 300,
		'height' => 400,
		'resizingType' => 'fill',
		'gravity' => 'sm',
	]);

	expect($options)->toBeOptions();
	expect($options->toString())->toBe('width:300/height:400/resizing_type:fill/gravity:sm');
});

test('can handle complex options', function (): void {
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

test('magic __toString behaves like toString method', function (): void {
	$options = createOptions();
	$options->setPreset('sharp')
		->setResize('fill', 300, 400, false);

	expect($options->toString())->toBe((string) $options);
});

test('boolean values are converted to 1 and 0', function (): void {
	$options = createOptions();
	$options->setEnlarge(true)
		->setAutoRotate(false)
		->setStripMetadata(true);

	expect($options->toString())->toBe('enlarge:1/auto_rotate:0/strip_metadata:1');
});

test('options requiring base64 encoding are properly encoded', function (): void {
	$options = createOptions();

	// Test watermark URL
	$watermarkUrl = 'https://example.com/watermark.png';
	$options->setWatermarkUrl($watermarkUrl);

	$encodedUrl = base64_encode($watermarkUrl);
	expect($options->toString())->toContain("watermark_url:{$encodedUrl}");
	expect($options->getWatermarkUrl())->toBe($watermarkUrl);

	// Test watermark text
	$text = 'Sample watermark text';
	$options->setWatermarkText($text);

	$encodedText = base64_encode($text);
	expect($options->toString())->toContain("watermark_text:{$encodedText}");
	expect($options->getWatermarkText()[0])->toBe($text);

	// Test style
	$style = 'filter:blur(10px)';
	$options->setStyle($style);

	$encodedStyle = base64_encode($style);
	expect($options->toString())->toContain("style:{$encodedStyle}");
	expect($options->getStyle())->toBe($style);

	// Test fallback image URL
	$fallbackUrl = 'https://example.com/fallback.png';
	$options->setFallbackImageUrl($fallbackUrl);

	$encodedFallbackUrl = base64_encode($fallbackUrl);
	expect($options->toString())->toContain("fallback_image_url:{$encodedFallbackUrl}");
	expect($options->getFallbackImageUrl())->toBe($fallbackUrl);

	// Test filename with encoded flag
	$filename = 'image.png';
	$options->setFilename($filename);

	$encodedFilename = base64_encode($filename);
	expect($options->toString())->toContain("filename:{$encodedFilename}:1");
	expect($options->getFilename())->toBe($filename);
});

test('setFilename encodes and decodes correctly with encode=true', function (): void {
	$options = createOptions();
	$filename = 'test image @ 2024.png';
	$options->setFilename($filename, true);

	$encoded = base64_encode($filename);
	expect($options->toString())->toContain("filename:{$encoded}:1");
	expect($options->getFilename())->toBe($filename);
});

test('setFilename stores and decodes correctly with encode=false', function (): void {
	$options = createOptions();
	$filename = 'test image @ 2024.png';
	$options->setFilename($filename, false);

	$urlEncoded = urlencode($filename);
	expect($options->toString())->toContain("filename:{$urlEncoded}:0");
	expect($options->getFilename())->toBe($filename);
});
