<?php
/**
 * Manifest
 *
 * The dynamic manifest file is constructed here
 */

/* Load WordPress */
include_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

/* Get the date of the last post modified (what we use for our manifest timestamp) */
$args = array(
	'orderby' => 'modified',
	'order' => 'DESC',
	'post_status' => 'publish',
	'post_type' => array( 'any' ),
	'posts_per_page' => 1
);

$wp_appcache_query = new WP_Query( $args );

while ( $wp_appcache_query->have_posts() ) {
	$wp_appcache_query->the_post();
	$wp_appcache_last_modified_content = get_the_modified_date( 'Y-m-d H:i:s' );
}

header("Content-Type: text/cache-manifest");

echo "CACHE MANIFEST

# version $wp_appcache_last_modified_content v1

CACHE:

# html files
/

NETWORK:
*";