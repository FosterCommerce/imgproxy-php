<?php

namespace fostercommerce\imgproxy;

class UrlBuilder
{
	/**
	 * imgproxy server base URL
	 */
	protected string $baseUrl;

	/**
	 * @param string $baseUrl The base URL for imgproxy
	 * @param string|null $key Key for signing URLs
	 * @param string|null $salt Salt for signing URLs
	 * @param bool $encode Whether to base64 encode source URLs
	 */
	public function __construct(
		string $baseUrl,
		protected ?string $key = null,
		protected ?string $salt = null,
		protected bool $encode = true
	) {
		$this->baseUrl = rtrim($baseUrl, '/');
	}

	/**
	 * Build URL for processing an image
	 *
	 * @param string $sourceUrl The source image URL
	 * @param Options|null $options Processing options
	 * @param string|null $extension Resulting image extension
	 * @return string The complete imgproxy URL
	 */
	public function buildUrl(string $sourceUrl, ?Options $options = null, ?string $extension = null): string
	{
		$options ??= new Options();
		$optionsString = (string) $options;

		// Prepare path to be signed
		$path = '/' . $optionsString;

		if ($this->encode) {
			// Base64 encode the source URL
			$encodedUrl = rtrim(strtr(base64_encode($sourceUrl), '+/', '-_'), '=');
			$path .= '/' . $encodedUrl;

			if ($extension !== null) {
				$path .= '.' . $extension;
			}
		} else {
			if (str_contains($sourceUrl, '@') || str_contains($sourceUrl, '?')) {
				// Escape query string and @ symbol in source URL
				$sourceUrl = str_replace('?', '%3F', $sourceUrl);
				$sourceUrl = str_replace('@', '%40', $sourceUrl);
			}

			$path .= '/plain/' . $sourceUrl;

			if ($extension !== null) {
				$path .= '@' . $extension;
			}
		}

		// Generate signature if key and salt are provided
		$signature = $this->key && $this->salt ? $this->generateSignature($path) : 'unsafe';

		return $this->baseUrl . '/' . $signature . $path;
	}

	/**
	 * Generate signature for the given path
	 */
	protected function generateSignature(string $path): string
	{
		$keyBin = pack('H*', $this->key);
		$saltBin = pack('H*', $this->salt);

		$signature = hash_hmac('sha256', $saltBin . $path, $keyBin, true);

		return rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
	}
}
