<?php

function hfc_donate_init_internal() {
   add_rewrite_rule('^ipn/?','index.php?hfc_donate_api=1','top');
}
add_action( 'init', 'hfc_donate_init_internal' );

function hfc_donate_query_vars( $query_vars ) {
    $query_vars[] = 'hfc_donate_api';
    return $query_vars;
}

add_filter( 'query_vars', 'hfc_donate_query_vars' );

function hfc_donate_parse_request( &$wp )
{
    if ( array_key_exists( 'hfc_donate_api', $wp->query_vars ) ) {
        include 'hfc_donate_reporting.php';
        exit();
    }
    return;
}
add_action( 'parse_request', 'hfc_donate_parse_request' );
?>
