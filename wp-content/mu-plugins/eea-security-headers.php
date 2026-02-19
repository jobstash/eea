<?php
/**
 * Plugin Name: EEA Security Headers
 * Description: Adds security response headers (HSTS, X-Content-Type-Options, etc.).
 * Author: EEA
 */

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	return;
}

add_filter( 'wp_headers', function ( $headers ) {
	$headers['X-Content-Type-Options']      = 'nosniff';
	$headers['X-XSS-Protection']            = '1; mode=block';
	$headers['Referrer-Policy']             = 'strict-origin-when-cross-origin';
	$headers['Permissions-Policy']         = 'geolocation=(), microphone=(), camera=()';

	if ( function_exists( 'is_ssl' ) && is_ssl() ) {
		$headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
	}

	return $headers;
}, 10, 1 );
