<?php
/**
 * This file contains the Option Storage class.
 *
 * @package ImageCDN
 */

namespace ImageEngine;

use ImageEngine\PhpSdk\Storage\StorageInterface;

class OptionStorage implements StorageInterface {
	public const PREFIX = 'image_cdn_';

	/**
	 * Get the value of an option.
	 *
	 * @param string $key The name of the option.
	 *
	 * @return mixed The value of the option.
	 */
	public function get( string $key ) {
		return get_option( self::PREFIX . $key );
	}

	/**
	 * Set the value of an option.
	 *
	 * @param string $key The name of the option.
	 * @param mixed  $value       The value of the option.
	 *
	 * @return bool True if the option was updated, false otherwise.
	 */
	public function set(string $key, string $value ) {
		return update_option( self::PREFIX . $key, $value );
	}

	/**
	 * Delete an option.
	 *
	 * @param string $key The name of the option.
	 *
	 * @return bool True if the option was deleted, false otherwise.
	 */
	public function delete($key ) {
		return delete_option( self::PREFIX . $key );
	}
}

