<?php
/*
Plugin Name: HFC Donate
Plugin URI: http://www.frankriccobono.com
Description: Allows for multiple levels for PayPal Donation.
Version: 1.0
Author: Frank Riccobono
Author URI: http://www.frankriccobono.com
License: MIT
*/

include 'hfc_donate_options.php';

add_shortcode('hfc_donation_levels', 'hfc_donation_levels_form_shortcode');
add_shortcode('level', 'hfc_donation_level_shortcode');

function hfc_donation_levels_form_shortcode( $attributes, $content = null ) {
	return render_html_snippet(
		'_hfc_form.html',
		array(
			'levels' => do_shortcode(shortcode_unautop($content)),
			'donate_user_email' => get_option('donate_paypal_email'),
			'organization_name' => get_option('organization_name')
		)
	);
}

function hfc_donation_level_shortcode($attributes, $content = null){
	extract(shortcode_atts(
		array(
			'amount' => 0,
			'label' => '',
		), 
		$attributes
	));
	
	return render_html_snippet(
		'_hfc_form_level.html',
		array(
			'amount' => $amount,
			'label' => $label,
		)
	);
}

function render_html_snippet($html_file, $vars) {
	$template = wp_remote_retrieve_body( 
		wp_remote_get( 
			plugins_url() . '/hfc_donate/' .$html_file
	));
	foreach($vars as $key => $value){
		$template = str_replace("{{".$key."}}", $value, $template);
	}
	return $template;
}