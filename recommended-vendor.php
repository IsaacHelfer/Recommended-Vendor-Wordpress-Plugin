<?php

/**
 * Plugin Name: Recommended Vendor
 * Description: This plugin is a form for users to fill out and give feedback as to why they recommend a certain vendor
 * Version: 1.0.0
 * Author: Isaac Helfer
*/

// Exit if accessed directly
if ( ! defined('ABSPATH') ) 
{
	exit;
}

require 'RecommendedVendorPlugin.php';
require 'ChangeVendorDataMetaBox.php';

$vendorPlugin = new RecommendedVendorPlugin();
$metaBox = new ChangeVendorDataMetaBox();

// if vendor form submited
if ( ! empty( $_POST ) && $_SERVER['REQUEST_URI'] === '/form/' ) {
	// Prevents putting empty data in the custom post type
	if ( array_key_exists( 'vendor_name', $_POST ) ) {
		// make the post
		$post_id = $vendorPlugin->make_post( $_POST['vendor_name'], implode( '<br>', $_POST ) );
		
		// add data to post_meta
		foreach ( $_POST as $form_data ) {
			add_post_meta( $post_id, array_search( $form_data, $_POST ), $form_data );
		}

		// redirect the user to the vendors page
		wp_redirect( 'test-website/vendors' );

		exit;
	}
}

if ( ! empty( $_POST) && $_SERVER['PHP_SELF'] === '/wp-admin/post.php' ) {
	if ( ! empty( $_GET ) ) {
		//$metaBox->update_data( (int) $_GET['post'], 'vendor_name', 'test' );
	}
}