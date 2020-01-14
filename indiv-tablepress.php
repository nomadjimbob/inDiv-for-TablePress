<?php
/*
Plugin Name: inDiv for TablePress
Plugin URI: https://jamescollins.com.au/resources/indiv-for-tablepress
Description: Custom Extension for TablePress to automatically wrap the table in a DIV element. Add in_div=true to your tables to enclose your TablePress tables in a DIV with the class indiv_tablepress.
Version: 1.0.1
Author: James Collins
Author URI: https://jamescollins.com.au/
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

/*
 * Usage and possible parameters:
 * [table id=1 in_div=true /]
 *
 * in_div: Whether the table will be enclosed in a div.
 */

add_filter( 'tablepress_table_output', 'indiv_conversion', 10, 3 );
add_filter( 'tablepress_shortcode_table_default_shortcode_atts', 'tablepress_add_shortcode_parameter_indiv_conversion' );

/**
 * Add Extension's parameters as a valid parameters to the [table /] Shortcode.
 */
function tablepress_add_shortcode_parameter_indiv_conversion( $default_atts ) {
	$default_atts['in_div'] = false;
	return $default_atts;
}

/**
 * Enclose the table in a div, if Shortcode parameter is set,
 */
function indiv_conversion ( $output, $table, $render_options ) {
	if ( $render_options['indiv'] ) {
		$output = '<div class="indiv_tablepress">'.$output.'</div>';
	}

	return $output;
}
