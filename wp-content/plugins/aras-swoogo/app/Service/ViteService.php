<?php
namespace Aras\Swoogo\Service;

// For a real-world example check here:
// https://github.com/wp-bond/bond/blob/master/src/Tooling/Vite.php
// https://github.com/wp-bond/boilerplate/tree/master/app/themes/boilerplate

// you might check @vitejs/plugin-legacy if you need to support older browsers
// https://github.com/vitejs/vite/tree/main/packages/plugin-legacy

class ViteService {

	/**
	 * build/dist directory
	 *
	 * @var string
	 */
	private $base;

	/**
	 * base url
	 *
	 * @var string
	 */
	private $url;

	/**
	 * url for the development server
	 *
	 * @var string
	 */
	private $devHost;


	public function __construct( 
		string $base = '', 
		string $url = '', 
		string $devHost = 'https://localhost:5173'
		)
	{
		$this->base = $base;
		$this->url = $url;
		$this->devHost = $devHost;
	}

	public function enqueue( $entry, $footer = false )
	{
		$action = $footer ? 'wp_footer' : 'wp_head';
		add_action( $action, function() use($entry){
			echo $this->vite( $entry );
		});
	}

	public function render( $entry )
	{
		echo $this->vite( $entry );
	}


	// Prints all the html entries needed for Vite

	function vite(string $entry): string
	{

		return "<!-- vite include -->"
			. "\n" . $this->jsTag($entry)
			. "\n" . $this->jsPreloadImports($entry)
			. "\n" . $this->cssTag($entry);
	}


	// Some dev/prod mechanism would exist in your project

	function isDev(string $entry): bool
	{
		// This method is very useful for the local server
		// if we try to access it, and by any means, didn't started Vite yet
		// it will fallback to load the production files from manifest
		// so you still navigate your site as you intended!

		static $exists = null;
		if ($exists !== null) {
			return $exists;
		}
		if (strpos($_SERVER['HTTP_HOST'], '.local') === false) {
			return $exists = false;
		}
		$handle = curl_init($this->devHost . '/src/' . $entry);
		
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_NOBODY, true);

		curl_exec($handle);
		$error = curl_errno($handle);
		curl_close($handle);

		return $exists = !$error;
	}


	// Helpers to print tags

	function jsTag(string $entry): string
	{
		$url = $this->isDev($entry)
			? $this->devHost . '/' . $entry
			: $this->assetUrl($entry);

		if (!$url) {
			return '';
		}
		return '<script type="module" crossorigin src="'
			. $url
			. '"></script>';
	}

	function jsPreloadImports(string $entry): string
	{
		if ($this->isDev($entry)) {
			return '';
		}

		$res = '';
		foreach ($this->importsUrls($entry) as $url) {
			$res .= '<link rel="modulepreload" href="'
				. $url
				. '">';
		}
		return $res;
	}

	function cssTag(string $entry): string
	{
		// not needed on dev, it's inject by Vite
		if ($this->isDev($entry)) {
			return '';
		}

		$tags = '';
		foreach ($this->cssUrls($entry) as $url) {
			$tags .= '<link rel="stylesheet" href="'
				. $url
				. '">';
		}
		return $tags;
	}


	// Helpers to locate files

	function getManifest(): array
	{
		static $manifest;
		if( isset($manifest) ){
			return $manifest;
		}
		$manifest = [];
		$manifest_file = $this->base.'/.vite/manifest.json';
		
		if( file_exists($manifest_file) ){
			try {
				$content = file_get_contents($manifest_file);
				$manifest = json_decode($content, true);
			}catch(\Exception $e){
				
			}
		}
		return $manifest;
	}

	function assetUrl(string $entry): string
	{
		$manifest = $this->getManifest();

		return isset($manifest[$entry])
			? $this->url . $manifest[$entry]['file']
			: '';
	}

	function importsUrls(string $entry): array
	{
		$urls = [];
		$manifest = $this->getManifest();

		if (!empty($manifest[$entry]['imports'])) {
			foreach ($manifest[$entry]['imports'] as $imports) {
				$urls[] = $this->url . $manifest[$imports]['file'];
			}
		}
		return $urls;
	}

	function cssUrls(string $entry): array
	{
		$urls = [];
		$manifest = $this->getManifest();

		if (!empty($manifest[$entry]['css'])) {
			foreach ($manifest[$entry]['css'] as $file) {
				$urls[] = $this->url . $file;
			}
		}
		return $urls;
	}
}