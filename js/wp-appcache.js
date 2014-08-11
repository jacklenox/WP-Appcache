/**
 * WP Appcache listens to site updates via the Heartbeat API.
 *
 * If something changes, the cache is updated.
 */
'use strict';
window.onload = function() {
	var currentSession, pageVisible;
	currentSession = window.sessionStorage.getItem( 'wp_appcache_session' );
	pageVisible = window.sessionStorage.getItem( 'wp_appcache_page_visible' );

	// The page should be visible
	if ( currentSession === 'true' && pageVisible === 'true' ) {
		document.getElementsByTagName('body')[0].style.visibility = 'visible';
	}
};

/*global jQuery:false */
jQuery( document ).ready( function( $ ) {
	// Listen for changes to the Application Cache
	var appCache = window.applicationCache;
	appCache.addEventListener( 'updateready', function( e ) {
		if ( appCache.status == appCache.UPDATEREADY ) {
			appCache.swapCache();
		}
	}, false );

	// Tell the cache to check for updates when the heart beats
	$( document ).on( 'heartbeat-tick', function() {
		appCache.update();
	});
});