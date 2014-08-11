<?php
/**
 * Manifest
 *
 * The dynamic manifest file is constructed here
 */

/* Load WordPress */
include_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

header( 'Content-Type: text/cache-manifest' );

echo 'CACHE MANIFEST

# version ' . get_option( '_wp_appcache_manifest_timestamp' ) . ' v1

CACHE:

# html files
/

NETWORK:
*';