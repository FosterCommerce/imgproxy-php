<?php

namespace fostercommerce\imgproxy;

class Options implements \Stringable
{
	/**
	 * Stores all set options and their values
	 *
	 * @var array<string, mixed>
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
			$method = 'set' . ucfirst($option);
			if (method_exists($this, $method)) {
				$this->{$method}($value);
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
			if (is_array($values)) {
				$values = implode($this->argumentsSeparator, array_map(fn ($value): string => $this->formatValue($value), $values));
			} else {
				$values = $this->formatValue($values);
			}

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
	 * Set width
	 */
	public function setWidth(int $width): self
	{
		$this->options['width'] = [$width];
		return $this;
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
	 * Set min width
	 */
	public function setMinWidth(int $width): self
	{
		$this->options['min-width'] = [$width];
		return $this;
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
	 * Set DPR (device pixel ratio)
	 */
	public function setDpr(float $dpr): self
	{
		$this->options['dpr'] = [$dpr];
		return $this;
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
	 * Set auto rotate
	 */
	public function setAutoRotate(bool $autoRotate): self
	{
		$this->options['auto_rotate'] = [$autoRotate];
		return $this;
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
	 * Set background
	 */
	public function setBackground(string $color): self
	{
		$this->options['background'] = [$color];
		return $this;
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
	 * Set sharpen
	 */
	public function setSharpen(float $sigma): self
	{
		$this->options['sharpen'] = [$sigma];
		return $this;
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
	 * Set strip metadata
	 */
	public function setStripMetadata(bool $strip): self
	{
		$this->options['strip_metadata'] = [$strip];
		return $this;
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
	 * Set strip color profile
	 */
	public function setStripColorProfile(bool $strip): self
	{
		$this->options['strip_color_profile'] = [$strip];
		return $this;
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
	 * Set format quality
	 */
	public function setFormatQuality(string $format, int $quality): self
	{
		$this->options['format_quality'] = [$format, $quality];
		return $this;
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
	 * Set format
	 */
	public function setFormat(string $format): self
	{
		$this->options['format'] = [$format];
		return $this;
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
	 * Set resizing algorithm
	 */
	public function setResizingAlgorithm(string $algorithm): self
	{
		$this->options['resizing_algorithm'] = [$algorithm];
		return $this;
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
	 * Set cache buster
	 */
	public function setCacheBuster(string $buster): self
	{
		$this->options['cache_buster'] = [$buster];
		return $this;
	}

	/**
	 * Set filename
	 */
	public function setFilename(string $filename): self
	{
		$this->options['filename'] = [$filename];
		return $this;
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
	 * Set skip processing
	 */
	public function setSkipProcessing(bool $skip): self
	{
		$this->options['skip_processing'] = [$skip];
		return $this;
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
	 * Set return attachment
	 */
	public function setReturnAttachment(bool $attachment): self
	{
		$this->options['return_attachment'] = [$attachment];
		return $this;
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
	 * Set brightness (Pro)
	 */
	public function setBrightness(float $brightness): self
	{
		$this->options['brightness'] = [$brightness];
		return $this;
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
	 * Set saturation (Pro)
	 */
	public function setSaturation(float $saturation): self
	{
		$this->options['saturation'] = [$saturation];
		return $this;
	}

	/**
	 * Set monochrome (Pro)
	 */
	public function setMonochrome(bool $monochrome): self
	{
		$this->options['monochrome'] = [$monochrome];
		return $this;
	}

	/**
	 * Set duotone (Pro)
	 */
	public function setDuotone(string $highlight, string $shadow): self
	{
		$this->options['duotone'] = [$highlight, $shadow];
		return $this;
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
	 * Set colorize (Pro)
	 */
	public function setColorize(string $color, ?float $mix = null): self
	{
		$values = [$color];
		if ($mix !== null) {
			$values[] = $mix;
		}

		$this->options['colorize'] = $values;
		return $this;
	}

	/**
	 * Set gradient (Pro)
	 */
	public function setGradient(string $colors, ?string $direction = null, ?string $opacity = null): self
	{
		$values = [$colors];
		if ($direction !== null) {
			$values[] = $direction;
		}

		if ($opacity !== null) {
			$values[] = $opacity;
		}

		$this->options['gradient'] = $values;
		return $this;
	}

	/**
	 * Set watermark URL (Pro)
	 */
	public function setWatermarkUrl(string $url): self
	{
		$this->options['watermark_url'] = [$url];
		return $this;
	}

	/**
	 * Set watermark text (Pro)
	 */
	public function setWatermarkText(string $text, ?string $font = null, ?float $fontSize = null, ?string $color = null, ?bool $wrap = null): self
	{
		$values = [$text];
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
	 * Set watermark size (Pro)
	 */
	public function setWatermarkSize(string $size): self
	{
		$this->options['watermark_size'] = [$size];
		return $this;
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
	 * Set style (Pro)
	 */
	public function setStyle(string $style): self
	{
		$this->options['style'] = [$style];
		return $this;
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
	 * Set JPEG options (Pro)
	 */
	public function setJpegOptions(?bool $progressive = null, ?bool $noSubsample = null, ?string $tubingMode = null): self
	{
		$values = [];
		if ($progressive !== null) {
			$values[] = $progressive;
		}

		if ($noSubsample !== null) {
			$values[] = $noSubsample;
		}

		if ($tubingMode !== null) {
			$values[] = $tubingMode;
		}

		$this->options['jpeg_options'] = $values;
		return $this;
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
	 * Set WebP options (Pro)
	 */
	public function setWebpOptions(?bool $lossless = null, ?int $nearLossless = null): self
	{
		$values = [];
		if ($lossless !== null) {
			$values[] = $lossless;
		}

		if ($nearLossless !== null) {
			$values[] = $nearLossless;
		}

		$this->options['webp_options'] = $values;
		return $this;
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
	 * Set page (Pro)
	 */
	public function setPage(int $page): self
	{
		$this->options['page'] = [$page];
		return $this;
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
	 * Set disable animation (Pro)
	 */
	public function setDisableAnimation(bool $disable): self
	{
		$this->options['disable_animation'] = [$disable];
		return $this;
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
	 * Set video thumbnail keyframes (Pro)
	 */
	public function setVideoThumbnailKeyframes(bool $value): self
	{
		$this->options['video_thumbnail_keyframes'] = [$value];
		return $this;
	}

	/**
	 * Set video thumbnail tile (Pro)
	 */
	public function setVideoThumbnailTile(string $tile, ?string $columns = null, ?string $rows = null): self
	{
		$values = [$tile];
		if ($columns !== null) {
			$values[] = $columns;
		}

		if ($rows !== null) {
			$values[] = $rows;
		}

		$this->options['video_thumbnail_tile'] = $values;
		return $this;
	}

	/**
	 * Set video thumbnail animation (Pro)
	 */
	public function setVideoThumbnailAnimation(string $framesMix, ?int $fps = null, ?bool $reverse = null): self
	{
		$values = [$framesMix];
		if ($fps !== null) {
			$values[] = $fps;
		}

		if ($reverse !== null) {
			$values[] = $reverse;
		}

		$this->options['video_thumbnail_animation'] = $values;
		return $this;
	}

	/**
	 * Set fallback image URL (Pro)
	 */
	public function setFallbackImageUrl(string $url): self
	{
		$this->options['fallback_image_url'] = [$url];
		return $this;
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
	 * Set max src resolution (Pro)
	 */
	public function setMaxSrcResolution(int $resolution): self
	{
		$this->options['max_src_resolution'] = [$resolution];
		return $this;
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
	 * Set max animation frames (Pro)
	 */
	public function setMaxAnimationFrames(int $frames): self
	{
		$this->options['max_animation_frames'] = [$frames];
		return $this;
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
}
