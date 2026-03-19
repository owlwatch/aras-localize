<?php

if (! defined('ABSPATH')) {
	exit;
}

class ArasSupportMatrixViteService
{
	private $base;
	private $url;
	private $dev_host;

	public function __construct($base, $url, $dev_host = 'http://localhost:5173')
	{
		$this->base = untrailingslashit($base);
		$this->url = trailingslashit($url);
		$this->dev_host = untrailingslashit($dev_host);
	}

	public function render($entry)
	{
		echo $this->vite($entry); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function vite($entry)
	{
		return "<!-- aras-support-matrix -->\n"
			. $this->js_tag($entry) . "\n"
			. $this->preload_tags($entry) . "\n"
			. $this->css_tags($entry);
	}

	private function is_dev($entry)
	{
		static $exists = null;

		if ($exists !== null) {
			return $exists;
		}

		$host = (string) wp_parse_url(home_url(), PHP_URL_HOST);

		if (strpos($host, '.local') === false) {
			return $exists = false;
		}

		$handle = curl_init($this->dev_host . '/' . ltrim($entry, '/'));

		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_NOBODY, true);
		curl_setopt($handle, CURLOPT_TIMEOUT, 1);
		curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);

		curl_exec($handle);
		$error = curl_errno($handle);
		curl_close($handle);

		return $exists = ! $error;
	}

	private function js_tag($entry)
	{
		if ($this->is_dev($entry)) {
			return '<script type="module" src="' . esc_url($this->dev_host . '/@vite/client') . '"></script>'
				. '<script type="module" src="' . esc_url($this->dev_host . '/' . ltrim($entry, '/')) . '"></script>';
		}

		$asset_url = $this->asset_url($entry);

		if (! $asset_url) {
			return '';
		}

		return '<script type="module" crossorigin src="' . esc_url($asset_url) . '"></script>';
	}

	private function preload_tags($entry)
	{
		if ($this->is_dev($entry)) {
			return '';
		}

		$manifest = $this->manifest();
		$item = $manifest[$entry] ?? null;

		if (! $item || empty($item['imports'])) {
			return '';
		}

		$tags = '';

		foreach ($item['imports'] as $import) {
			if (! empty($manifest[$import]['file'])) {
				$tags .= '<link rel="modulepreload" href="' . esc_url($this->url . $manifest[$import]['file']) . '">';
			}
		}

		return $tags;
	}

	private function css_tags($entry)
	{
		if ($this->is_dev($entry)) {
			return '';
		}

		$css_files = $this->collect_css_files($entry);

		if (empty($css_files)) {
			return '';
		}

		$tags = '';

		foreach ($css_files as $file) {
			$tags .= '<link rel="stylesheet" href="' . esc_url($this->url . $file) . '">';
		}

		return $tags;
	}

	private function collect_css_files($entry, &$seen = array())
	{
		$manifest = $this->manifest();
		$item = $manifest[$entry] ?? null;

		if (! $item || isset($seen[$entry])) {
			return array();
		}

		$seen[$entry] = true;
		$files = array();

		if (! empty($item['css']) && is_array($item['css'])) {
			$files = array_merge($files, $item['css']);
		}

		if (! empty($item['imports']) && is_array($item['imports'])) {
			foreach ($item['imports'] as $import) {
				$files = array_merge($files, $this->collect_css_files($import, $seen));
			}
		}

		return array_values(array_unique($files));
	}

	private function manifest()
	{
		static $manifest = null;

		if ($manifest !== null) {
			return $manifest;
		}

		$manifest_path = $this->base . '/.vite/manifest.json';

		if (! file_exists($manifest_path)) {
			return $manifest = array();
		}

		$contents = file_get_contents($manifest_path);
		$decoded = json_decode($contents, true);

		return $manifest = is_array($decoded) ? $decoded : array();
	}

	private function asset_url($entry)
	{
		$manifest = $this->manifest();

		if (empty($manifest[$entry]['file'])) {
			return '';
		}

		return $this->url . $manifest[$entry]['file'];
	}
}
