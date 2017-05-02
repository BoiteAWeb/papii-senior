<?php
/*
Script Name: PAPII Senior
Author: Julio Potier
Author URI: http://boiteaweb.fr
Version 1.1
*/
require( 'wp-load.php' );
header("Content-Type:text/plain");

$format = isset( $_GET['format'] ) ? $_GET['format'] : 'php';
$sort = isset( $_GET['sort'] );
$resp = wp_remote_get( 'http://profiles.wordpress.org/' . $_GET['profile'] );
if ( ! is_wp_error( $resp ) && 200 === $resp['response']['code'] ){
	$doc = new DOMDocument();
	@$doc->loadHTML( $resp['body'] );
	$divs = $doc->getElementById( 'main-column' )->getElementsByTagName( 'div' );
	$plugins = array();
	$blocks = array( 'homemade', 'favorites' );
	$i=0;
	foreach ( $divs as $div ) {
		if ( strstr( $div->getAttribute( 'class' ), 'main-plugins' ) ) {
			$lis = $div->getElementsByTagName( 'li' );
			foreach ( $lis as $li ) {
				$as = $li->getElementsByTagName( 'a' );
				foreach ( $as as $a ) {
					$plugins[ $blocks[ $i ] ][ $a->nodeValue ] = $a->getAttribute( 'href' );
				}
			}
			if ( $sort ) {
				ksort( $plugins[ $blocks[ $i ] ] );
			}
			++$i;
		}
	}
	if ( $format == 'json' ) {
		die( json_encode( $plugins ) );
	} else {
		die( var_export( $plugins ) );
	}
}
die( json_encode( array( 'error'=>true ) ) );  
