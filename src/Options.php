<?php

namespace fostercommerce\imgproxy;

class Options implements \Stringable
{
	/**
	 * Stores all set options and their values
	 *
	 * @var array<string, array<array-key, mixed>>
	 */
	protected array $options = [];

	/**
	 * Option separator for the URL
	 */
	protected string $optionSeparator = '/';

	/**
	 * Arguments separator for option values
	 */
	protected string $argumentsSeparator = ':';

	/**
	 * @param array<string, mixed> $options
	 */
	public function __construct(array $options = [])
	{
		foreach ($options as $option => $value) {
			$method = 'set' . $this->toPascalCase($option);
			if (method_exists($this, $method)) {
				// We want to make sure that we can set the individual arguments from an associative array or a regular array.
				$args = is_array($value) ? $value : [$value];
				$this->{$method}(...$args);
			}
		}
	}

	/**
	 * Magic method to convert object to string
	 */
	public function __toString(): string
	{
		return $this->toString();
	}

	/**
	 * Get the string representation of all options
	 */
	public function toString(): string
	{
		$parts = [];

		foreach ($this->options as $option => $values) {
			$values = implode($this->argumentsSeparator, array_map(fn ($value): string => $this->formatValue($value), $values));

			$parts[] = $option . ($values !== '' ? $this->argumentsSeparator . $values : '');
		}

		return implode($this->optionSeparator, $parts);
	}

	/**
	 * Set resizing type
	 *
	 * @param string $type fit|fill|fill-down|force|auto
	 */
	public function setResizingType(string $type): self
	{
		$this->options['resizing_type'] = [$type];
		return $this;
	}

	/**
	 * Get resizing type
	 */
	public function getResizingType(): ?string
	{
		/** @var ?string $value */
		$value = $this->options['resizing_type'][0] ?? null;

		return $value;
	}

	/**
	 * Set width
	 */
	public function setWidth(int $width): self
	{
		$this->options['width'] = [$width];
		return $this;
	}

	/**
	 * Get width
	 */
	public function getWidth(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['width'][0] ?? null;

		return $value;
	}

	/**
	 * Set height
	 */
	public function setHeight(int $height): self
	{
		$this->options['height'] = [$height];
		return $this;
	}

	/**
	 * Get height
	 */
	public function getHeight(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['height'][0] ?? null;

		return $value;
	}

	/**
	 * Set min width
	 */
	public function setMinWidth(int $width): self
	{
		$this->options['min-width'] = [$width];
		return $this;
	}

	/**
	 * Get min width
	 */
	public function getMinWidth(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['min-width'][0] ?? null;

		return $value;
	}

	/**
	 * Set min height
	 */
	public function setMinHeight(int $height): self
	{
		$this->options['min-height'] = [$height];
		return $this;
	}

	/**
	 * Get min height
	 */
	public function getMinHeight(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['min-height'][0] ?? null;

		return $value;
	}

	/**
	 * Set zoom
	 *
	 * @param float|array<int, float> $zoom Either single value for both dimensions or [x, y]
	 */
	public function setZoom($zoom): self
	{
		$this->options['zoom'] = is_array($zoom) ? $zoom : [$zoom];
		return $this;
	}

	/**
	 * Get zoom
	 *
	 * @return float|array<int, float>|null
	 */
	public function getZoom(): float|array|null
	{
		/** @var float|array<int, float>|null $value */
		$value = $this->options['zoom'] ?? null;

		return $value;
	}

	/**
	 * Set DPR (device pixel ratio)
	 */
	public function setDpr(float $dpr): self
	{
		$this->options['dpr'] = [$dpr];
		return $this;
	}

	/**
	 * Get DPR (device pixel ratio)
	 */
	public function getDpr(): ?float
	{
		/** @var ?float $value */
		$value = $this->options['dpr'][0] ?? null;

		return $value;
	}

	/**
	 * Set enlarge
	 */
	public function setEnlarge(bool $enlarge): self
	{
		$this->options['enlarge'] = [$enlarge];
		return $this;
	}

	/**
	 * Get enlarge
	 */
	public function getEnlarge(): ?bool
	{
		/** @var ?bool $value */
		$value = $this->options['enlarge'][0] ?? null;

		return $value;
	}

	/**
	 * Set extend
	 */
	public function setExtend(bool $extend, ?string $gravity = null): self
	{
		$values = [$extend];
		if ($gravity !== null) {
			$values[] = $gravity;
		}

		$this->options['extend'] = $values;
		return $this;
	}

	/**
	 * Get extend
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getExtend(): ?array
	{
		return $this->options['extend'] ?? null;
	}

	/**
	 * Set extend aspect ratio
	 */
	public function setExtendAspectRatio(bool $extend, ?string $gravity = null): self
	{
		$values = [$extend];
		if ($gravity !== null) {
			$values[] = $gravity;
		}

		$this->options['extend_aspect_ratio'] = $values;
		return $this;
	}

	/**
	 * Get extend aspect ratio
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getExtendAspectRatio(): ?array
	{
		return $this->options['extend_aspect_ratio'] ?? null;
	}

	/**
	 * Set gravity
	 */
	public function setGravity(string $type, ?int $xOffset = null, ?int $yOffset = null): self
	{
		$values = [$type];
		if ($xOffset !== null) {
			$values[] = $xOffset;
		}

		if ($yOffset !== null) {
			$values[] = $yOffset;
		}

		$this->options['gravity'] = $values;
		return $this;
	}

	/**
	 * Get gravity
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getGravity(): ?array
	{
		return $this->options['gravity'] ?? null;
	}

	/**
	 * Set crop
	 */
	public function setCrop(int $width, int $height, ?int $x = null, ?int $y = null): self
	{
		$values = [$width, $height];
		if ($x !== null) {
			$values[] = $x;
		}

		if ($y !== null) {
			$values[] = $y;
		}

		$this->options['crop'] = $values;
		return $this;
	}

	/**
	 * Get crop
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getCrop(): ?array
	{
		return $this->options['crop'] ?? null;
	}

	/**
	 * Set padding
	 */
	public function setPadding(int $top, ?int $right = null, ?int $bottom = null, ?int $left = null): self
	{
		$values = [$top];
		if ($right !== null) {
			$values[] = $right;
		}

		if ($bottom !== null) {
			$values[] = $bottom;
		}

		if ($left !== null) {
			$values[] = $left;
		}

		$this->options['padding'] = $values;
		return $this;
	}

	/**
	 * Get padding
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getPadding(): ?array
	{
		return $this->options['padding'] ?? null;
	}

	/**
	 * Set trim
	 *
	 * @param int|string $threshold
	 */
	public function setTrim($threshold, ?string $color = null, ?bool $equalHor = null, ?bool $equalVer = null): self
	{
		$values = [$threshold];
		if ($color !== null) {
			$values[] = $color;
		}

		if ($equalHor !== null) {
			$values[] = $equalHor;
		}

		if ($equalVer !== null) {
			$values[] = $equalVer;
		}

		$this->options['trim'] = $values;
		return $this;
	}

	/**
	 * Get trim
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getTrim(): ?array
	{
		return $this->options['trim'] ?? null;
	}

	/**
	 * Set auto rotate
	 */
	public function setAutoRotate(bool $autoRotate): self
	{
		$this->options['auto_rotate'] = [$autoRotate];
		return $this;
	}

	/**
	 * Get auto rotate
	 */
	public function getAutoRotate(): ?bool
	{
		/** @var ?bool $value */
		$value = $this->options['auto_rotate'][0] ?? null;

		return $value;
	}

	/**
	 * Set rotate
	 */
	public function setRotate(int $angle): self
	{
		$this->options['rotate'] = [$angle];
		return $this;
	}

	/**
	 * Get rotate
	 */
	public function getRotate(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['rotate'][0] ?? null;

		return $value;
	}

	/**
	 * Set background
	 */
	public function setBackground(string $color): self
	{
		$this->options['background'] = [$color];
		return $this;
	}

	/**
	 * Get background
	 */
	public function getBackground(): ?string
	{
		/** @var ?string $value */
		$value = $this->options['background'][0] ?? null;

		return $value;
	}

	/**
	 * Set blur
	 */
	public function setBlur(float $sigma): self
	{
		$this->options['blur'] = [$sigma];
		return $this;
	}

	/**
	 * Get blur
	 */
	public function getBlur(): ?float
	{
		/** @var ?float $value */
		$value = $this->options['blur'][0] ?? null;

		return $value;
	}

	/**
	 * Set sharpen
	 */
	public function setSharpen(float $sigma): self
	{
		$this->options['sharpen'] = [$sigma];
		return $this;
	}

	/**
	 * Get sharpen
	 */
	public function getSharpen(): ?float
	{
		/** @var ?float $value */
		$value = $this->options['sharpen'][0] ?? null;

		return $value;
	}

	/**
	 * Set pixelate
	 */
	public function setPixelate(int $size): self
	{
		$this->options['pixelate'] = [$size];
		return $this;
	}

	/**
	 * Get pixelate
	 */
	public function getPixelate(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['pixelate'][0] ?? null;

		return $value;
	}

	/**
	 * Set watermark
	 */
	public function setWatermark(
		float $opacity,
		?string $position = null,
		?float $xOffset = null,
		?float $yOffset = null,
		?float $scale = null
	): self {
		$values = [$opacity];
		if ($position !== null) {
			$values[] = $position;
		}

		if ($xOffset !== null) {
			$values[] = $xOffset;
		}

		if ($yOffset !== null) {
			$values[] = $yOffset;
		}

		if ($scale !== null) {
			$values[] = $scale;
		}

		$this->options['watermark'] = $values;
		return $this;
	}

	/**
	 * Get watermark
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getWatermark(): ?array
	{
		return $this->options['watermark'] ?? null;
	}

	/**
	 * Set strip metadata
	 */
	public function setStripMetadata(bool $strip): self
	{
		$this->options['strip_metadata'] = [$strip];
		return $this;
	}

	/**
	 * Get strip metadata
	 */
	public function getStripMetadata(): ?bool
	{
		/** @var ?bool $value */
		$value = $this->options['strip_metadata'][0] ?? null;

		return $value;
	}

	/**
	 * Set keep copyright
	 */
	public function setKeepCopyright(bool $keep): self
	{
		$this->options['keep_copyright'] = [$keep];
		return $this;
	}

	/**
	 * Get keep copyright
	 */
	public function getKeepCopyright(): ?bool
	{
		/** @var ?bool $value */
		$value = $this->options['keep_copyright'][0] ?? null;

		return $value;
	}

	/**
	 * Set strip color profile
	 */
	public function setStripColorProfile(bool $strip): self
	{
		$this->options['strip_color_profile'] = [$strip];
		return $this;
	}

	/**
	 * Get strip color profile
	 */
	public function getStripColorProfile(): ?bool
	{
		/** @var ?bool $value */
		$value = $this->options['strip_color_profile'][0] ?? null;

		return $value;
	}

	/**
	 * Set quality
	 */
	public function setQuality(int $quality): self
	{
		$this->options['quality'] = [$quality];
		return $this;
	}

	/**
	 * Get quality
	 */
	public function getQuality(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['quality'][0] ?? null;

		return $value;
	}

	/**
	 * Set format quality
	 */
	public function setFormatQuality(string $format, int $quality): self
	{
		$this->options['format_quality'] = [$format, $quality];
		return $this;
	}

	/**
	 * Get format quality
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getFormatQuality(): ?array
	{
		return $this->options['format_quality'] ?? null;
	}

	/**
	 * Set max bytes
	 */
	public function setMaxBytes(int $bytes): self
	{
		$this->options['max_bytes'] = [$bytes];
		return $this;
	}

	/**
	 * Get max bytes
	 */
	public function getMaxBytes(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['max_bytes'][0] ?? null;

		return $value;
	}

	/**
	 * Set format
	 */
	public function setFormat(string $format): self
	{
		$this->options['format'] = [$format];
		return $this;
	}

	/**
	 * Get format
	 */
	public function getFormat(): ?string
	{
		/** @var ?string $value */
		$value = $this->options['format'][0] ?? null;

		return $value;
	}

	/**
	 * Set resize
	 */
	public function setResize(
		string $type,
		?int $width = null,
		?int $height = null,
		?bool $enlarge = null,
		?bool $extend = null
	): self {
		$values = [$type];
		if ($width !== null) {
			$values[] = $width;
		}

		if ($height !== null) {
			$values[] = $height;
		}

		if ($enlarge !== null) {
			$values[] = $enlarge;
		}

		if ($extend !== null) {
			$values[] = $extend;
		}

		$this->options['resize'] = $values;
		return $this;
	}

	/**
	 * Get resize
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getResize(): ?array
	{
		return $this->options['resize'] ?? null;
	}

	/**
	 * Set size
	 */
	public function setSize(
		?int $width = null,
		?int $height = null,
		?bool $enlarge = null,
		?bool $extend = null
	): self {
		$values = [];
		if ($width !== null) {
			$values[] = $width;
		}

		if ($height !== null) {
			$values[] = $height;
		}

		if ($enlarge !== null) {
			$values[] = $enlarge;
		}

		if ($extend !== null) {
			$values[] = $extend;
		}

		$this->options['size'] = $values;
		return $this;
	}

	/**
	 * Get size
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getSize(): ?array
	{
		return $this->options['size'] ?? null;
	}

	/**
	 * Set resizing algorithm
	 */
	public function setResizingAlgorithm(string $algorithm): self
	{
		$this->options['resizing_algorithm'] = [$algorithm];
		return $this;
	}

	/**
	 * Get resizing algorithm
	 */
	public function getResizingAlgorithm(): ?string
	{
		/** @var ?string $value */
		$value = $this->options['resizing_algorithm'][0] ?? null;

		return $value;
	}

	/**
	 * Set enforce thumbnail
	 */
	public function setEnforceThumbnail(bool $enforce): self
	{
		$this->options['enforce_thumbnail'] = [$enforce];
		return $this;
	}

	/**
	 * Get enforce thumbnail
	 */
	public function getEnforceThumbnail(): ?bool
	{
		/** @var ?bool $value */
		$value = $this->options['enforce_thumbnail'][0] ?? null;

		return $value;
	}

	/**
	 * Set preset
	 *
	 * @param string|array<int, string> $presets
	 */
	public function setPreset($presets): self
	{
		$this->options['preset'] = is_array($presets) ? $presets : [$presets];
		return $this;
	}

	/**
	 * Get preset
	 *
	 * @return string|array<array-key, string>|null
	 */
	public function getPreset(): string|array|null
	{
		/** @var string|array<array-key, string>|null $value */
		$value = $this->options['preset'] ?? null;

		return $value;
	}

	/**
	 * Set cache buster
	 */
	public function setCacheBuster(string $buster): self
	{
		$this->options['cache_buster'] = [$buster];
		return $this;
	}

	/**
	 * Get cache buster
	 */
	public function getCacheBuster(): ?string
	{
		/** @var ?string $value */
		$value = $this->options['cache_buster'][0] ?? null;

		return $value;
	}

	/**
	 * Set filename
	 */
	public function setFilename(string $filename): self
	{
		$this->options['filename'] = [base64_encode($filename), true];
		return $this;
	}

	/**
	 * Get filename
	 */
	public function getFilename(): ?string
	{
		/** @var ?string $value */
		$value = $this->options['filename'][0] ?? null;

		return $value !== null ? (base64_decode($value, true) ?: null) : null;
	}

	/**
	 * Set expires
	 */
	public function setExpires(int $seconds): self
	{
		$this->options['expires'] = [$seconds];
		return $this;
	}

	/**
	 * Get expires
	 */
	public function getExpires(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['expires'][0] ?? null;

		return $value;
	}

	/**
	 * Set skip processing
	 *
	 * @param string|array<int, string> $extensions Format extensions to skip processing
	 */
	public function setSkipProcessing($extensions): self
	{
		$this->options['skip_processing'] = is_array($extensions) ? $extensions : [$extensions];
		return $this;
	}

	/**
	 * Get skip processing
	 *
	 * @return string|array<array-key, string>|null
	 */
	public function getSkipProcessing(): string|array|null
	{
		/** @var string|array<array-key, string>|null $value */
		$value = $this->options['skip_processing'] ?? null;

		return $value;
	}

	/**
	 * Set raw
	 */
	public function setRaw(bool $raw): self
	{
		$this->options['raw'] = [$raw];
		return $this;
	}

	/**
	 * Get raw
	 */
	public function getRaw(): ?bool
	{
		/** @var ?bool $value */
		$value = $this->options['raw'][0] ?? null;

		return $value;
	}

	/**
	 * Set return attachment
	 */
	public function setReturnAttachment(bool $attachment): self
	{
		$this->options['return_attachment'] = [$attachment];
		return $this;
	}

	/**
	 * Get return attachment
	 */
	public function getReturnAttachment(): ?bool
	{
		/** @var ?bool $value */
		$value = $this->options['return_attachment'][0] ?? null;

		return $value;
	}

	/**
	 * Set background alpha (Pro)
	 */
	public function setBackgroundAlpha(float $alpha): self
	{
		$this->options['background_alpha'] = [$alpha];
		return $this;
	}

	/**
	 * Get background alpha (Pro)
	 */
	public function getBackgroundAlpha(): ?float
	{
		/** @var ?float $value */
		$value = $this->options['background_alpha'][0] ?? null;

		return $value;
	}

	/**
	 * Set adjust (Pro)
	 */
	public function setAdjust(?float $brightness = null, ?float $contrast = null, ?float $saturation = null): self
	{
		$values = [];
		if ($brightness !== null) {
			$values[] = $brightness;
		}

		if ($contrast !== null) {
			$values[] = $contrast;
		}

		if ($saturation !== null) {
			$values[] = $saturation;
		}

		$this->options['adjust'] = $values;
		return $this;
	}

	/**
	 * Get adjust (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getAdjust(): ?array
	{
		return $this->options['adjust'] ?? null;
	}

	/**
	 * Set brightness (Pro)
	 */
	public function setBrightness(float $brightness): self
	{
		$this->options['brightness'] = [$brightness];
		return $this;
	}

	/**
	 * Get brightness (Pro)
	 */
	public function getBrightness(): ?float
	{
		/** @var ?float $value */
		$value = $this->options['brightness'][0] ?? null;

		return $value;
	}

	/**
	 * Set contrast (Pro)
	 */
	public function setContrast(float $contrast): self
	{
		$this->options['contrast'] = [$contrast];
		return $this;
	}

	/**
	 * Get contrast (Pro)
	 */
	public function getContrast(): ?float
	{
		/** @var ?float $value */
		$value = $this->options['contrast'][0] ?? null;

		return $value;
	}

	/**
	 * Set saturation (Pro)
	 */
	public function setSaturation(float $saturation): self
	{
		$this->options['saturation'] = [$saturation];
		return $this;
	}

	/**
	 * Get saturation (Pro)
	 */
	public function getSaturation(): ?float
	{
		/** @var ?float $value */
		$value = $this->options['saturation'][0] ?? null;

		return $value;
	}

	/**
	 * Set monochrome (Pro)
	 */
	public function setMonochrome(float $intensity, ?string $color = null): self
	{
		$values = [$intensity];
		if ($color !== null) {
			$values[] = $color;
		}

		$this->options['monochrome'] = $values;
		return $this;
	}

	/**
	 * Get monochrome (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getMonochrome(): ?array
	{
		return $this->options['monochrome'] ?? null;
	}

	/**
	 * Set duotone (Pro)
	 */
	public function setDuotone(float $intensity, ?string $color1 = null, ?string $color2 = null): self
	{
		$values = [$intensity];
		if ($color1 !== null) {
			$values[] = $color1;
		}

		if ($color2 !== null) {
			$values[] = $color2;
		}

		$this->options['duotone'] = $values;
		return $this;
	}

	/**
	 * Get duotone (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getDuotone(): ?array
	{
		return $this->options['duotone'] ?? null;
	}

	/**
	 * Set unsharp masking (Pro)
	 */
	public function setUnsharpMasking(float $sigma, ?float $amount = null, ?float $threshold = null): self
	{
		$values = [$sigma];
		if ($amount !== null) {
			$values[] = $amount;
		}

		if ($threshold !== null) {
			$values[] = $threshold;
		}

		$this->options['unsharp_masking'] = $values;
		return $this;
	}

	/**
	 * Get unsharp masking (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getUnsharpMasking(): ?array
	{
		return $this->options['unsharp_masking'] ?? null;
	}

	/**
	 * Set blur detections (Pro)
	 */
	public function setBlurDetections(string $type, ?float $sigma = null): self
	{
		$values = [$type];
		if ($sigma !== null) {
			$values[] = $sigma;
		}

		$this->options['blur_detections'] = $values;
		return $this;
	}

	/**
	 * Get blur detections (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getBlurDetections(): ?array
	{
		return $this->options['blur_detections'] ?? null;
	}

	/**
	 * Set draw detections (Pro)
	 */
	public function setDrawDetections(string $type, ?string $color = null, ?float $thickness = null): self
	{
		$values = [$type];
		if ($color !== null) {
			$values[] = $color;
		}

		if ($thickness !== null) {
			$values[] = $thickness;
		}

		$this->options['draw_detections'] = $values;
		return $this;
	}

	/**
	 * Get draw detections (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getDrawDetections(): ?array
	{
		return $this->options['draw_detections'] ?? null;
	}

	/**
	 * Set objects position (Pro)
	 */
	public function setObjectsPosition(string $type, ?bool $expand = null, ?float $gravity = null, ?bool $noOverlap = null): self
	{
		$values = [$type];
		if ($expand !== null) {
			$values[] = $expand;
		}

		if ($gravity !== null) {
			$values[] = $gravity;
		}

		if ($noOverlap !== null) {
			$values[] = $noOverlap;
		}

		$this->options['objects_position'] = $values;
		return $this;
	}

	/**
	 * Get objects position (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getObjectsPosition(): ?array
	{
		return $this->options['objects_position'] ?? null;
	}

	/**
	 * Set colorize (Pro)
	 */
	public function setColorize(float $opacity, ?string $color = null, ?bool $keepAlpha = null): self
	{
		$values = [$opacity];
		if ($color !== null) {
			$values[] = $color;
		}

		if ($keepAlpha !== null) {
			$values[] = $keepAlpha;
		}

		$this->options['colorize'] = $values;
		return $this;
	}

	/**
	 * Get colorize (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getColorize(): ?array
	{
		return $this->options['colorize'] ?? null;
	}

	/**
	 * Set gradient (Pro)
	 */
	public function setGradient(float $opacity, ?string $color = null, ?string $direction = null, ?float $start = null, ?float $stop = null): self
	{
		$values = [$opacity];
		if ($color !== null) {
			$values[] = $color;
		}

		if ($direction !== null) {
			$values[] = $direction;
		}

		if ($start !== null) {
			$values[] = $start;
		}

		if ($stop !== null) {
			$values[] = $stop;
		}

		$this->options['gradient'] = $values;
		return $this;
	}

	/**
	 * Get gradient (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getGradient(): ?array
	{
		return $this->options['gradient'] ?? null;
	}

	/**
	 * Set watermark URL (Pro)
	 */
	public function setWatermarkUrl(string $url): self
	{
		$this->options['watermark_url'] = [base64_encode($url)];
		return $this;
	}

	/**
	 * Get watermark URL (Pro)
	 */
	public function getWatermarkUrl(): ?string
	{
		/** @var ?string $value */
		$value = $this->options['watermark_url'][0] ?? null;

		return $value !== null ? (base64_decode($value, true) ?: null) : null;
	}

	/**
	 * Set watermark text (Pro)
	 */
	public function setWatermarkText(string $text, ?string $font = null, ?float $fontSize = null, ?string $color = null, ?bool $wrap = null): self
	{
		$values = [base64_encode($text)];
		if ($font !== null) {
			$values[] = $font;
		}

		if ($fontSize !== null) {
			$values[] = $fontSize;
		}

		if ($color !== null) {
			$values[] = $color;
		}

		if ($wrap !== null) {
			$values[] = $wrap;
		}

		$this->options['watermark_text'] = $values;
		return $this;
	}

	/**
	 * Get watermark text (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getWatermarkText(): ?array
	{
		$options = $this->options['watermark_text'] ?? null;

		if ($options !== null && isset($options[0])) {
			/** @var string $value */
			$value = $options[0];
			$options[0] = (base64_decode($value, true) ?: null);
		}

		return $options;
	}

	/**
	 * Set watermark size (Pro)
	 */
	public function setWatermarkSize(int $width, int $height): self
	{
		$this->options['watermark_size'] = [$width, $height];
		return $this;
	}

	/**
	 * Get watermark size (Pro)
	 *
	 * @return ?int[]
	 */
	public function getWatermarkSize(): ?array
	{
		/** @var ?int[] $value */
		$value = $this->options['watermark_size'] ?? null;

		return $value;
	}

	/**
	 * Set watermark rotate (Pro)
	 */
	public function setWatermarkRotate(float $angle): self
	{
		$this->options['watermark_rotate'] = [$angle];
		return $this;
	}

	/**
	 * Get watermark rotate (Pro)
	 */
	public function getWatermarkRotate(): ?float
	{
		/** @var ?float $value */
		$value = $this->options['watermark_rotate'][0] ?? null;

		return $value;
	}

	/**
	 * Set watermark shadow (Pro)
	 */
	public function setWatermarkShadow(float $opacity, ?float $sigma = null, ?int $xOffset = null, ?int $yOffset = null): self
	{
		$values = [$opacity];
		if ($sigma !== null) {
			$values[] = $sigma;
		}

		if ($xOffset !== null) {
			$values[] = $xOffset;
		}

		if ($yOffset !== null) {
			$values[] = $yOffset;
		}

		$this->options['watermark_shadow'] = $values;
		return $this;
	}

	/**
	 * Get watermark shadow (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getWatermarkShadow(): ?array
	{
		return $this->options['watermark_shadow'] ?? null;
	}

	/**
	 * Set style (Pro)
	 */
	public function setStyle(string $style): self
	{
		$this->options['style'] = [base64_encode($style)];
		return $this;
	}

	/**
	 * Get style (Pro)
	 */
	public function getStyle(): ?string
	{
		/** @var ?string $value */
		$value = $this->options['style'][0] ?? null;

		return $value !== null ? (base64_decode($value, true) ?: null) : null;
	}

	/**
	 * Set DPI (Pro)
	 */
	public function setDpi(int $dpi): self
	{
		$this->options['dpi'] = [$dpi];
		return $this;
	}

	/**
	 * Get DPI (Pro)
	 */
	public function getDpi(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['dpi'][0] ?? null;

		return $value;
	}

	/**
	 * Set JPEG options (Pro)
	 */
	public function setJpegOptions(
		?bool $progressive = null,
		?bool $noSubsample = null,
		?bool $trellisQuant = null,
		?bool $overshootDeringing = null,
		?bool $optimizeScans = null,
		?int $quantTable = null
	): self {
		$values = [];
		if ($progressive !== null) {
			$values[] = $progressive;
		}

		if ($noSubsample !== null) {
			$values[] = $noSubsample;
		}

		if ($trellisQuant !== null) {
			$values[] = $trellisQuant;
		}

		if ($overshootDeringing !== null) {
			$values[] = $overshootDeringing;
		}

		if ($optimizeScans !== null) {
			$values[] = $optimizeScans;
		}

		if ($quantTable !== null) {
			$values[] = $quantTable;
		}

		$this->options['jpeg_options'] = $values;
		return $this;
	}

	/**
	 * Get JPEG options (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getJpegOptions(): ?array
	{
		return $this->options['jpeg_options'] ?? null;
	}

	/**
	 * Set PNG options (Pro)
	 */
	public function setPngOptions(?bool $interlaced = null, ?bool $quantize = null, ?int $quantizeColors = null): self
	{
		$values = [];
		if ($interlaced !== null) {
			$values[] = $interlaced;
		}

		if ($quantize !== null) {
			$values[] = $quantize;
		}

		if ($quantizeColors !== null) {
			$values[] = $quantizeColors;
		}

		$this->options['png_options'] = $values;
		return $this;
	}

	/**
	 * Get PNG options (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getPngOptions(): ?array
	{
		return $this->options['png_options'] ?? null;
	}

	/**
	 * Set WebP options (Pro)
	 */
	public function setWebpOptions(?string $compression = null, ?bool $smartSubsample = null): self
	{
		$values = [];
		if ($compression !== null) {
			$values[] = $compression;
		}

		if ($smartSubsample !== null) {
			$values[] = $smartSubsample;
		}

		$this->options['webp_options'] = $values;
		return $this;
	}

	/**
	 * Get WebP options (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getWebpOptions(): ?array
	{
		return $this->options['webp_options'] ?? null;
	}

	/**
	 * Set autoquality (Pro)
	 */
	public function setAutoquality(string $method, ?string $target = null, ?int $minQuality = null, ?int $maxQuality = null, ?bool $allowedError = null): self
	{
		$values = [$method];
		if ($target !== null) {
			$values[] = $target;
		}

		if ($minQuality !== null) {
			$values[] = $minQuality;
		}

		if ($maxQuality !== null) {
			$values[] = $maxQuality;
		}

		if ($allowedError !== null) {
			$values[] = $allowedError;
		}

		$this->options['autoquality'] = $values;
		return $this;
	}

	/**
	 * Get autoquality (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getAutoquality(): ?array
	{
		return $this->options['autoquality'] ?? null;
	}

	/**
	 * Set page (Pro)
	 */
	public function setPage(int $page): self
	{
		$this->options['page'] = [$page];
		return $this;
	}

	/**
	 * Get page (Pro)
	 */
	public function getPage(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['page'][0] ?? null;

		return $value;
	}

	/**
	 * Set pages (Pro)
	 */
	public function setPages(int $pages): self
	{
		$this->options['pages'] = [$pages];
		return $this;
	}

	/**
	 * Get pages (Pro)
	 */
	public function getPages(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['pages'][0] ?? null;

		return $value;
	}

	/**
	 * Set disable animation (Pro)
	 */
	public function setDisableAnimation(bool $disable): self
	{
		$this->options['disable_animation'] = [$disable];
		return $this;
	}

	/**
	 * Get disable animation (Pro)
	 */
	public function getDisableAnimation(): ?bool
	{
		/** @var ?bool $value */
		$value = $this->options['disable_animation'][0] ?? null;

		return $value;
	}

	/**
	 * Set video thumbnail second (Pro)
	 */
	public function setVideoThumbnailSecond(float $second): self
	{
		$this->options['video_thumbnail_second'] = [$second];
		return $this;
	}

	/**
	 * Get video thumbnail second (Pro)
	 */
	public function getVideoThumbnailSecond(): ?float
	{
		/** @var ?float $value */
		$value = $this->options['video_thumbnail_second'][0] ?? null;

		return $value;
	}

	/**
	 * Set video thumbnail keyframes (Pro)
	 */
	public function setVideoThumbnailKeyframes(bool $value): self
	{
		$this->options['video_thumbnail_keyframes'] = [$value];
		return $this;
	}

	/**
	 * Get video thumbnail keyframes (Pro)
	 */
	public function getVideoThumbnailKeyframes(): ?bool
	{
		/** @var ?bool $value */
		$value = $this->options['video_thumbnail_keyframes'][0] ?? null;

		return $value;
	}

	/**
	 * Set video thumbnail tile (Pro)
	 */
	public function setVideoThumbnailTile(
		float $step,
		?int $columns = null,
		?int $rows = null,
		?int $tileWidth = null,
		?int $tileHeight = null,
		?bool $extendTile = null,
		?bool $trim = null,
		?bool $fill = null,
		?float $focusX = null,
		?float $focusY = null
	): self {
		$values = [$step];
		if ($columns !== null) {
			$values[] = $columns;
		}

		if ($rows !== null) {
			$values[] = $rows;
		}

		if ($tileWidth !== null) {
			$values[] = $tileWidth;
		}

		if ($tileHeight !== null) {
			$values[] = $tileHeight;
		}

		if ($extendTile !== null) {
			$values[] = $extendTile;
		}

		if ($trim !== null) {
			$values[] = $trim;
		}

		if ($fill !== null) {
			$values[] = $fill;
		}

		if ($focusX !== null) {
			$values[] = $focusX;
		}

		if ($focusY !== null) {
			$values[] = $focusY;
		}

		$this->options['video_thumbnail_tile'] = $values;
		return $this;
	}

	/**
	 * Get video thumbnail tile (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getVideoThumbnailTile(): ?array
	{
		return $this->options['video_thumbnail_tile'] ?? null;
	}

	/**
	 * Set video thumbnail animation (Pro)
	 */
	public function setVideoThumbnailAnimation(
		float $step,
		?int $delay = null,
		?int $frames = null,
		?int $frameWidth = null,
		?int $frameHeight = null,
		?bool $extendFrame = null,
		?bool $trim = null,
		?bool $fill = null,
		?float $focusX = null,
		?float $focusY = null
	): self {
		$values = [$step];
		if ($delay !== null) {
			$values[] = $delay;
		}

		if ($frames !== null) {
			$values[] = $frames;
		}

		if ($frameWidth !== null) {
			$values[] = $frameWidth;
		}

		if ($frameHeight !== null) {
			$values[] = $frameHeight;
		}

		if ($extendFrame !== null) {
			$values[] = $extendFrame;
		}

		if ($trim !== null) {
			$values[] = $trim;
		}

		if ($fill !== null) {
			$values[] = $fill;
		}

		if ($focusX !== null) {
			$values[] = $focusX;
		}

		if ($focusY !== null) {
			$values[] = $focusY;
		}

		$this->options['video_thumbnail_animation'] = $values;
		return $this;
	}

	/**
	 * Get video thumbnail animation (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getVideoThumbnailAnimation(): ?array
	{
		return $this->options['video_thumbnail_animation'] ?? null;
	}

	/**
	 * Set fallback image URL (Pro)
	 */
	public function setFallbackImageUrl(string $url): self
	{
		$this->options['fallback_image_url'] = [base64_encode($url)];
		return $this;
	}

	/**
	 * Get fallback image URL (Pro)
	 */
	public function getFallbackImageUrl(): ?string
	{
		/** @var ?string $value */
		$value = $this->options['fallback_image_url'][0] ?? null;

		return $value !== null ? (base64_decode($value, true) ?: null) : null;
	}

	/**
	 * Set hashsum (Pro)
	 */
	public function setHashsum(string $type, ?string $hashsum = null): self
	{
		$values = [$type];
		if ($hashsum !== null) {
			$values[] = $hashsum;
		}

		$this->options['hashsum'] = $values;
		return $this;
	}

	/**
	 * Get hashsum (Pro)
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function getHashsum(): ?array
	{
		return $this->options['hashsum'] ?? null;
	}

	/**
	 * Set max src resolution (Pro)
	 */
	public function setMaxSrcResolution(int $resolution): self
	{
		$this->options['max_src_resolution'] = [$resolution];
		return $this;
	}

	/**
	 * Get max src resolution (Pro)
	 */
	public function getMaxSrcResolution(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['max_src_resolution'][0] ?? null;

		return $value;
	}

	/**
	 * Set max src file size (Pro)
	 */
	public function setMaxSrcFileSize(int $size): self
	{
		$this->options['max_src_file_size'] = [$size];
		return $this;
	}

	/**
	 * Get max src file size (Pro)
	 */
	public function getMaxSrcFileSize(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['max_src_file_size'][0] ?? null;

		return $value;
	}

	/**
	 * Set max animation frames (Pro)
	 */
	public function setMaxAnimationFrames(int $frames): self
	{
		$this->options['max_animation_frames'] = [$frames];
		return $this;
	}

	/**
	 * Get max animation frames (Pro)
	 */
	public function getMaxAnimationFrames(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['max_animation_frames'][0] ?? null;

		return $value;
	}

	/**
	 * Set max animation frame resolution (Pro)
	 */
	public function setMaxAnimationFrameResolution(int $resolution): self
	{
		$this->options['max_animation_frame_resolution'] = [$resolution];
		return $this;
	}

	/**
	 * Get max animation frame resolution (Pro)
	 */
	public function getMaxAnimationFrameResolution(): ?int
	{
		/** @var ?int $value */
		$value = $this->options['max_animation_frame_resolution'][0] ?? null;

		return $value;
	}

	/**
	 * Format a value for string representation
	 */
	protected function formatValue(mixed $value): string
	{
		if ($value === true) {
			return '1';
		}

		if ($value === false) {
			return '0';
		}

		if ($value === null) {
			return '';
		}

		if (is_scalar($value)) {
			return (string) $value;
		}

		return '';
	}

	private function toPascalCase(string $input): string
	{
		return ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
	}
}
