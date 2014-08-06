<?php
/**
 * Plugin Name: WP AppCache
 * Plugin URI: http://haveposts.com
 * Description: Allows your site to leverage the awesomeness of AppCache!
 * Version: 0.1.1
 * Author: Jack Lenox
 * Author URI: http://haveposts.com
 * License: GPL2
 */

/**
 * Adds the reference to the manifest file to the <html> tag by way of language_attributes
 *
 * @since  0.1.1
 */
add_filter( 'language_attributes', 'wp_appcache_add_manifest_to_language_attributes' );

function wp_appcache_add_manifest_to_language_attributes( $output ) {
	return $output . ' manifest="' . plugins_url( 'manifest.php', __FILE__ ) . '"';
}