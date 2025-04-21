<?php

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOptions', fn () => $this->toBeInstanceOf(\fostercommerce\imgproxy\Options::class));

expect()->extend('toBeUrlBuilder', fn () => $this->toBeInstanceOf(\fostercommerce\imgproxy\UrlBuilder::class));

expect()->extend('toMatchSignedImgproxyUrl', function (string $baseUrl, string $optionsPattern, string $sourceUrl) {
	// Escape special regex characters in the baseUrl and sourceUrl
	$baseUrlEscaped = preg_quote($baseUrl, '~');
	$sourceUrlEscaped = preg_quote($sourceUrl, '~');

	// Create the pattern with optional extension
	$pattern = "~^{$baseUrlEscaped}/([A-Za-z0-9_-]+)/{$optionsPattern}/plain/{$sourceUrlEscaped}(@[a-z0-9]+)?$~";

	return $this->toMatch($pattern);
});

expect()->extend('toMatchSignedBase64ImgproxyUrl', function (string $baseUrl, string $optionsPattern, string $sourceUrl, ?string $extension = null) {
	// Escape special regex characters in the baseUrl
	$baseUrlEscaped = preg_quote($baseUrl, '~');

	// Generate the base64 encoded source URL
	$encodedUrl = rtrim(strtr(base64_encode($sourceUrl), '+/', '-_'), '=');
	$encodedUrlEscaped = preg_quote($encodedUrl, '~');

	// Create the pattern with optional extension
	$extensionPattern = $extension !== null ? preg_quote('.' . $extension, '~') : '(\\.[a-z0-9]+)?';
	$pattern = "~^{$baseUrlEscaped}/([A-Za-z0-9_-]+)/{$optionsPattern}/{$encodedUrlEscaped}{$extensionPattern}$~";

	return $this->toMatch($pattern);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function createOptions(array $options = []): \fostercommerce\imgproxy\Options
{
	return new \fostercommerce\imgproxy\Options($options);
}

function createUrlBuilder(string $baseUrl = 'https://imgproxy.example.com', ?string $key = null, ?string $salt = null, bool $encode = false, ?string $customSignature = null): \fostercommerce\imgproxy\UrlBuilder
{
	return new \fostercommerce\imgproxy\UrlBuilder($baseUrl, $key, $salt, $encode, $customSignature);
}
