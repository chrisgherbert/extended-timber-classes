<?php

namespace bermanco\ExtendedTimberClasses\Tools;

class Tools {

	/**
	 * Get human-readable file size
	 * @param  string $file File path (server only, no URLs)
	 * @return string|null  Friendly file size
	 */
	public static function get_file_size($file){

		if (file_exists($file)){
			return self::format_bytes(filesize($file));
		}

	}

	/**
	 * Auto format bytes into better human-readable value.
	 * Taken from http://stackoverflow.com/a/2510459/1667136
	 * @param  integer $bytes     File size in bytes
	 * @param  integer $precision Number of decimal points
	 * @return string             Human-readable file size
	 */
	public static function format_bytes($bytes, $precision = 1) { 

		$base = log($bytes, 1024);
		$suffixes = array('', 'KB', 'MB', 'GB', 'TB');   

		return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];

	} 

}