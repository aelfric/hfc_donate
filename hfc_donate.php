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
include 'hfc_donate_callback.php';

add_shortcode('hfc_donation_levels', 'hfc_donation_levels_form_shortcode');
add_shortcode('level', 'hfc_donation_level_shortcode');

function hfc_donation_levels_form_shortcode( $attributes, $content = null ) {
	return render_html_snippet(
		'_hfc_form.html',
		array(
			'levels' => do_shortcode(shortcode_unautop($content)),
			'donate_user_email' => get_option('donate_paypal_email'),
			'organization_name' => get_option('organization_name'),
			'paypal_url' => get_option('paypal_url'),
            'notify_url' => site_url('ipn'),
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
	$template = file_get_contents(
			WP_PLUGIN_DIR.'/hfc_donate/' .$html_file
	);
	foreach($vars as $key => $value){
		$template = str_replace("{{".$key."}}", $value, $template);
	}
	return $template;
}


global $hfc_donate_db_version;
register_activation_hook(__FILE__, 'hfc_donate_install');

function hfc_donate_install(){
    $table_name = "wp_payment_notifications";

    $sql = "CREATE TABLE $table_name (
        id INT Not Null Auto_Increment,
        Primary key(id),
        PaymentStatus VARCHAR(20),
        PaymentAmount Decimal(6,2),
        PayerEmail VARCHAR(50),
        FirstName VARCHAR(50),
        LastName VARCHAR(50),
        AddressStreet VARCHAR(50),
        ReceiverEmail VARCHAR(50),
        AddressStreet VARCHAR(50),
        AddressCity VARCHAR(50),
        AddressState VARCHAR(50),
        AddressZip VARCHAR(50),
        TransactionID VARCHAR(30),
        Cur_TimeStamp TIMESTAMP(8)
    );";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( "hfc_donate_db_version", $hfc_donate_db_version );
 }

 function hfc_donate_progress_widget($args) {
    echo $args['before_widget']; 
    echo $args['before_title'] . 'My Unique Widget' . $args['after_title'];
    echo render_html_snippet('_hfc_donate_widget.html', 
    array('goal' => get_option('goal'),
    'progress' => hfc_get_donated_amount()));
    echo $args['after_widget'];
 }

 function hfc_register_widgets(){
    wp_enqueue_style('donation_widget', plugins_url("/thermometer.css", __FILE__));
    wp_enqueue_script('donation_widget', plugins_url("thermometer.js",__FILE__), array('jquery'));
    wp_register_sidebar_widget('donation_progress', 'Donations', 'hfc_donate_progress_widget');
 }

 add_action('init', 'hfc_register_widgets');

 function hfc_get_donated_amount(){
    return 1250;
 }
