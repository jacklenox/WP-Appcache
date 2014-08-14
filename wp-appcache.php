<?php
/**
 * Plugin Name: WP Appcache
 * Plugin URI: http://haveposts.com
 * Description: Allows your site to leverage the awesomeness of the HTML 5 Application Cache.
 * Version: 0.1.3
 * Author: Jack Lenox
 * Author URI: http://haveposts.com
 * License: GPL2
 */

/**
 * Plugin activation
 *
 * Sets a site option with the current timestamp
 *
 * @since 0.1.1
 */
register_activation_hook( __FILE__, 'wp_appcache_update_timestamp' );

/**
 * Add the reference to the manifest file to the <html> tag by way of language_attributes
 *
 * @since 0.1.1
 */
if ( ! is_admin() && ! in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
	add_filter( 'init', wp_appcache_add_manifest );
	function wp_appcache_add_manifest() {
		if ( ! is_user_logged_in() ) {
			add_filter( 'language_attributes', 'wp_appcache_add_manifest_to_language_attributes' );

			// Set the timestamp as a session variable
			$session = session_id();
			if ( empty( $session ) ) session_start();
			$_SESSION['wp_appcache_manifest_timestamp'] = get_option( '_wp_appcache_manifest_timestamp' );

			function wp_appcache_add_manifest_to_language_attributes( $output ) {
				return $output . ' manifest="' . plugins_url( 'manifest.php', __FILE__ ) . '"';
			}
		}
	}
}

/**
 * Hook onto all relevant actions to update the manifest timestamp
 *
 * The idea here is that anything that might change the appearance of a website will update
 * the manifest.
 *
 * @since 0.1.1
 */

// Attachments
add_action( 'add_attachment', 'wp_appcache_update_timestamp' );
add_action( 'edit_attachment', 'wp_appcache_update_timestamp' );
add_action( 'delete_attachment', 'wp_appcache_update_timestamp' );

// Comments
add_action( 'commment_post', 'wp_appcache_update_timestamp' );
add_action( 'edit_comment', 'wp_appcache_update_timestamp' );
add_action( 'deleted_comment', 'wp_appcache_update_timestamp' );

// Options
add_action( 'added_option', 'wp_appcache_update_timestamp' );
add_action( 'deleted_option', 'wp_appcache_update_timestamp' );

// Plugins
add_action( 'activated_plugin', 'wp_appcache_update_timestamp' );
add_action( 'deactivated_plugin', 'wp_appcache_update_timestamp' );

// Posts and pages
add_action( 'save_post', 'wp_appcache_update_timestamp' );
add_action( 'deleted_post', 'wp_appcache_update_timestamp' );
add_action( 'trashed_post', 'wp_appcache_update_timestamp' );

// Themes
add_action( 'after_switch_theme', 'wp_appcache_update_timestamp' );

// Login/Logout
add_action( 'wp_login', 'wp_appcache_update_timestamp' );
add_action( 'wp_logout', 'wp_appcache_update_timestamp' );

function wp_appcache_update_timestamp() {
	update_option( '_wp_appcache_manifest_timestamp', current_time( 'mysql' ) );
}

/**
 * Load the WP Appcache JS
 *
 * @since 0.1.1
 */
add_action( 'wp_enqueue_scripts', 'wp_appcache_enqueue_javascript' );

function wp_appcache_enqueue_javascript() {
	wp_enqueue_script(
		'wp-appcache',
		plugins_url( '/js/wp-appcache.js', __FILE__ ),
		array( 'jquery', 'heartbeat' )
	);
}

/**
 * Load in wp_footer() the site's most recent timestamp
 *
 * @since 0.1.1
 */
add_action( 'wp_footer', 'wp_appcache_footer_timestamp' );

function wp_appcache_footer_timestamp() {
	echo '<div id="wp-appcache-timestamp" style="display: none">' . get_option( '_wp_appcache_manifest_timestamp' ) . '</div>';
}

/**
 * Speed up the heartbeat!
 *
 * @since 0.1.1
 */
add_filter( 'heartbeat_settings', 'wp_appcache_heartbeat_settings' );

function wp_appcache_heartbeat_settings( $settings ) {
	$settings['interval'] = 15;
	return $settings;
}

/**
 * Load in wp_head() the JS that handles certain AppCache sync issues
 *
 * So, this is what might appear like a messy chunk of JS and CSS. It is injected into the head
 * mainly in the interests of it running quickly (hence no jQuery here).
 *
 * What this does is check if the user is already engaged in a current session with the website.
 * If not, it runs a JS XMLHttpRequest almost as soon as the page starts to load to see if the
 * website in question has been updated 
 *
 * @since 0.1.1
 */
add_action( 'wp_head', 'wp_appcache_javascript_and_css' );

function wp_appcache_javascript_and_css() {
	$wp_appcache_js = "<script type='text/javascript'>
	var request, currentSession, siteUpdated, returningUser;

	// Has the user ever been here before?
	returningUser = window.localStorage.getItem( 'wp_appcache_returning_user' );
	
	// Some local storage items that help us deduce what the user's browser has cached
	siteUpdated = window.localStorage.getItem( 'wp_appcache_timestamp' );
	currentSession = window.sessionStorage.getItem( 'wp_appcache_session' );
	
	// If the user has never been here before, we set the meta so that things load normally
	if ( returningUser !== 'true' ) {
			window.sessionStorage.setItem( 'wp_appcache_session', true );
			window.sessionStorage.setItem( 'wp_appcache_page_visible', true );
			window.localStorage.setItem( 'wp_appcache_returning_user', true );

	// Otherwise they've been here before but it's a new session, things get interesting
	} else if ( currentSession !== 'true'  ) {

		// DOMParser function for browser compatibility
		( function ( DOMParser) {
		'use strict';
		var DOMParser_proto = DOMParser.prototype, real_parseFromString = DOMParser_proto.parseFromString;

		// Firefox/Opera/IE throw errors on unsupported types
		try {
			// WebKit returns null on unsupported types
			if ((new DOMParser).parseFromString('', 'text/html')) {
				// text/html parsing is natively supported
				return;
			}
		} catch (ex) {}

		DOMParser_proto.parseFromString = function(markup, type) {
			if (/^\s*text\/html\s*(?:;|$)/i.test(type)) {
				var doc = document.implementation.createHTMLDocument(''), doc_elt = doc.documentElement, first_elt;

				doc_elt.innerHTML = markup;
				first_elt = doc_elt.firstElementChild;

				if (doc_elt.childElementCount === 1 && first_elt.localName.toLowerCase() === 'html') {
					doc.replaceChild(first_elt, doc_elt);
				}

				return doc;
			} else {
				return real_parseFromString.apply(this, arguments);
			}
		};
		}( DOMParser ) );
		
		request = new XMLHttpRequest();

		request.onreadystatechange = function() {
			if ( request.readyState == 4 ) {
				if ( request.status == 200 ) {
					var parser = new DOMParser();
					var responseDoc = parser.parseFromString( request.responseText, 'text/html' );
					responseDocDate = responseDoc.getElementById('wp-appcache-timestamp');
					if ( siteUpdated < responseDocDate ) {
						// It's different, therefore repaint the page with the latest data
						document.open('text/html');
						document.write(request.responseText);
						document.close();
						document.getElementsByTagName('body')[0].style.visibility = 'visible';
						window.localStorage.setItem( 'wp_appcache_timestamp', responseDocDate );
					} else {
						// It's the same, just show it
						document.getElementsByTagName('body')[0].style.visibility = 'visible';
					}
				} else if ( request.status == 400 ) {
					document.getElementsByTagName('body')[0].style.visibility = 'visible';
				} else {
					document.getElementsByTagName('body')[0].style.visibility = 'visible';
				}

				// Update the local storage meta
				window.sessionStorage.setItem( 'wp_appcache_session', true );
				window.sessionStorage.setItem( 'wp_appcache_page_visible', true );
				window.localStorage.setItem( 'wp_appcache_returning_user', true );
			}
		}

		var timeInMs = Date.now();

		request.open('GET', '" . $_SERVER['REQUEST_URI'] . "?' + timeInMs, true);
		request.send();
	}
	</script>";
	echo $wp_appcache_js;

	$wp_appcache_css = '<style>body { visibility: hidden; }</style>';
	echo $wp_appcache_css;
}