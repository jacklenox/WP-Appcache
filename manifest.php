<?php
/**
 * Manifest
 *
 * The dynamic manifest file is constructed here
 */
$session = session_id();
if ( empty( $session ) ) session_start();

header( 'Content-Type: text/cache-manifest' );

echo 'CACHE MANIFEST

# version ' . $_SESSION['wp_appcache_manifest_timestamp'] . ' v1

CACHE:

# html files
/

NETWORK:
*';