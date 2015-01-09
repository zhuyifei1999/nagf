<?php
class WebCache {

	/**
	 * @param string $key
	 * @return bool
	 */
	private static function validKey($key) {
		return preg_match('/^[a-z0-9\-]+$/', $key);
	}

	/**
	 * Get string data from a url (or cache).
	 *
	 * @param string $key
	 * @param string $url
	 * @param int $expire How long this may be cached
	 * @return string
	 */
	public static function get($key, $url, $expire = 3600) {
		static $dir;
		if ($dir === null) {
			$dir = dirname(__DIR__) . '/cache';
		}

		if (!self::validKey($key)) {
			throw new Exception('Invalid key');
		}

		if (!is_writable($dir)) {
			throw new Exception('Unable to write to cache directory');
		}

		$cacheFile = "$dir/$key.cache";
		$hasCache = file_exists($cacheFile);

		if ($hasCache && filemtime($cacheFile) > (time() - $expire)) {
			// Cache file is new enough, use it.
			return file_get_contents($cacheFile);
		}

		// Fetch fresh copy from remote
		$value = file_get_contents($url);
		if ($value === false) {
			if ($hasCache) {
				// Keep using cache for now, remote failed
				return file_get_contents($cacheFile);
			}
			throw new Exception('Unable to fetch ' . $url);
		}

		$written = file_put_contents($cacheFile, $value, LOCK_EX);
		if ($written === false) {
			throw new Exception('Unable to write to cache file');
		}

		return $value;
	}
}
