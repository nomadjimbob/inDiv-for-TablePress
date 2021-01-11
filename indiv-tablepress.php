<?php
/**
 * WordPress plugin "inDiv for TablePress" main file, responsible for initiating the plugin
 *
 * @package inDivTablePress
 * @author James Collins
 * @version 1.0.3
 *
 *
 * Plugin Name: inDiv for TablePress
 * Plugin URI: https://jamescollins.com.au/resources/indiv-tablepress/
 * Description: Custom Extension for TablePress to automatically wrap the table in a DIV element. Add indiv=true to your tables to enclose your TablePress tables in a DIV with the class indiv_tablepress.
 * Version: 1.0.3
 * Requires at least: 5.3
 * Requires PHP: 5.6.20
 * Author: James Collins
 * Author URI: https://jamescollins.com.au/
 * Author email: james.collins@outlook.com.au
 * License: GPL 3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Donate URI: https://jamescollins.com.au/donate/
 *
 *
 * Copyright 2020-2021 James Collins
 *
 * inDiv for TablePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'Hey, no direct script access allowed around here' );

/**
 * inDivTablePress class
 * @package inDivTablePress
 * @author James Collins
 * @since 1.0.2
 */
abstract class inDivTablePress {

    /**
     * inDivTablePress version.
     *
     * Increases whenever a new plugin version is released.
     *
     * @since 1.0.2
     * @const string
     */
    const version = '1.0.2';

    /**
     * Start-up inDivTablePress (run on "tablepress_run").
     *
     * @since 1.0.2
     */
    public static function run() {
        // Exit early if inDivTablePress doesn't have to be loaded.
        if ( ( 'wp-login.php' === basename( $_SERVER['SCRIPT_FILENAME'] ) ) // Login screen
            || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST )
            || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
            return;
        }

        // Check if minimum requirements are fulfilled, currently WordPress 5.3.
        include( ABSPATH . WPINC . '/version.php' ); // Include an unmodified $wp_version.
        if ( version_compare( str_replace( '-src', '', $wp_version ), '5.3', '<' ) ) {
            
            // Show error notice to admins, if WP is not installed in the minimum required version, in which case TablePress will not work.
            if ( current_user_can( 'update_plugins' ) ) {
                add_action( 'admin_notices', array( 'inDivTablePress', 'show_minimum_requirements_error_notice' ) );
            }
            
            // And exit inDivTablePress.
            return;
        }
        
        // Add the admin menus for configuration options
        add_action( 'admin_menu', array( 'inDivTablePress', 'admin_add_menus'), 20 );
        add_action( 'admin_post_save_indivtablepress_options', array( 'inDivTablePress', 'admin_post_save_indivtablepress_options') );
        
        // Add the tablepress shortcode attributes
        add_filter( 'tablepress_table_output', array( 'inDivTablePress', 'indivtablepress_output'  ), 10, 3 );
        add_filter( 'tablepress_shortcode_table_default_shortcode_atts', array( 'inDivTablePress', 'indivtablepress_shortcode_atts' ) );        
    }
    
    /**
     * Adds the admin submenus to the admin page.
     *
     * @since 1.0.2
     */
    public static function admin_add_menus() {
        add_submenu_page( 'tablepress', __( 'inDiv Options', 'inDivTablePress' ), __( 'inDiv Options', 'inDivTablePress' ), 'manage_options', 'indivtablepress', array( 'inDivTablePress', 'admin_view' ) );
    }
    
    /**
     * Render the indiv table around the table
     *
     * @since 1.0.2
     */
    public static function indivtablepress_output( $output, $table, $render_options ) {
        if ( $render_options['in_div'] ) {
    		$output = '<div class="indiv_tablepress tablepress_in_div">'.$output.'</div>';
    	}
    
    	return $output;        
    }
    
    /**
     * Set the atts for the table
     *
     * @since 1.0.2
     */
    function indivtablepress_shortcode_atts( $default_atts ) {
        $indiv_default = false;
        
        if( get_option( 'indivtablepress_default' ) !== false && get_option( 'indivtablepress_default' ) ) {
            $indiv_default = true;
        }

    	$default_atts['in_div'] = $indiv_default;
    	return $default_atts;
    }
    
    /**
     * Show the admin page for the plugin.
     *
     * @since 1.0.2
     */
    public static function admin_view() {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        
        if(isset($_GET['notice']) && $_GET['notice'] == 'success') {
            echo '<div class="notice notice-success is-dismissible"><p><strong>Options saved successfully.</strong></p></div>';
        }
        
        add_meta_box('admin_view_metabox_options', __( 'inDiv Options', 'inDivTablePress' ), array( 'inDivTablePress', 'admin_view_metabox_options' ), 'indivtablepress', 'normal' );
        add_meta_box('admin_view_metabox_help', __( 'About inDiv', 'inDivTablePress' ), array( 'inDivTablePress', 'admin_view_metabox_help' ), 'indivtablepress', 'additional' );
        add_meta_box('admin_view_metabox_about', __( 'Author and License', 'inDivTablePress' ), array( 'inDivTablePress', 'admin_view_metabox_about' ), 'indivtablepress', 'side' );
        $data = array();
        
        ?>
        <div id="howto-metaboxes-general" class="wrap">
        <?php screen_icon('options-general'); ?>
        <h2>inDiv Table Options</h2>
        <p style="margin:0;color:#999">Version: <?php echo inDivTablePress::version ?></p>
        <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
            <?php $indiv_nonce_value = wp_create_nonce('indivtablepress_nonce'); ?>
            <input type="hidden" name="indivtablepress_nonce" value="<?php echo $indiv_nonce_value; ?>" />
            <input type="hidden" name="action" value="save_indivtablepress_options" />
        
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="postbox-container-2" class="postbox-container">
                        <?php do_meta_boxes('indivtablepress', 'normal', $data); ?>
                        <?php do_meta_boxes('indivtablepress', 'additional', $data); ?>
                        <p>
                            <input type="submit" value="Save Changes" class="button-primary" name="Submit"/>    
                        </p>
                    </div>
                    <div id="postbox-container-1" class="postbox-container">
                        <?php do_meta_boxes('indivtablepress', 'side', $data); ?>
                    </div>
                </div>
                <br class="clear"/>
            </div>  
        </form>
        </div>
        <?php
    }
    
    /**
     * Renders the options metabox
     *
     * @since 1.0.2
     */    
    public static function admin_view_metabox_options() {
        $option_default = '';
        if( get_option( 'indivtablepress_default' ) !== false && get_option( 'indivtablepress_default' ) ) {
            $option_default = 'checked="checked"';
        }
    
        ?>
        <table class="indivtablepress-postbox-table fixed">
        <tbody>
            <tr>
                <td><label for="option-default"><input type="checkbox" id="option-default" name="options[default]" <?php echo $option_default ?>> inDiv applied by default to all TablePress tables</label></td>
            </tr>
        </tbody>
        </table>        
        <?php
    }
            
    /**
     * Renders the help metabox
     *
     * @since 1.0.2
     */    
    public static function admin_view_metabox_help() {
        ?>
        <h2 style="padding-left:0;font-size:1.2rem">Using inDiv</h2>
        <p>There are 2 ways to apply inDiv to your TablePress tables:</p>
        <ul style="list-style-type:disc; margin-left:1rem;">
            <li>Turn on <strong>inDiv applied by default to all TablePress tables</strong> add the attribute <strong>indiv=false</strong> to the tables you do not want it applied to</li>
            <li>Add the attribute <strong>indiv=true</strong> to the tables you do want inDiv applied</li>
        </ul>
        <br class="clear" />
        <p>Adding the attribute to the TablePress shortcode is as easy as</p>
        <p><code>[table id=... indiv=true /]</code></p>
        <br class="clear" />
        <p>You can style the DIV element by customising the theme appearance or other CSS editing plugin. TablePress tables that have inDiv applied are wrapped in a DIV element with the class <code>indiv_tablepress</code>
        <br class="clear" />
        <h2 style="padding-left:0;font-size:1.2rem">Previous versions</h2>
        <p>This version is compatible with previous version attributes and classes, however administrators should change to the new shortcode attribute and classname.</p>
        <ul style="list-style-type:disc; margin-left:1rem;">
            <li>The class <code>tablepress_in_div</code> is now <code>indiv_tablepress</code></li>
            <li>The shortcode attribute <code>in_div</code> is now <code>indiv</code></li>
        </ul>
        <?php
    }
            
    /**
     * Renders the about metabox
     *
     * @since 1.0.2
     */
    public static function admin_view_metabox_about() {
        ?>
        <p>This plugin was written and developed by <a href="https://jamescollins.com.au/">James Collins</a>. It is licensed as Free Software under GNU General Public License 3 (GPL 3).</p>
        <p>If you like the plugin, then please <b><a href="https://jamescollins.com.au/donate">give a donation</a></b> and rate and review the plugin in the <a href="https://wordpress.org/plugins/indiv-for-tablepress/#reviews">WordPress Plugin Directory</a>.</p>
        <p>Donations and good ratings encourage the further development of this plugin and to provide countless hours of support. Any amount is really appreciated. Thanks!</p>
        <?php
    }
    
    /**
     * Saves the admin view settings
     *
     * @since 1.0.2
     */
    public static function admin_post_save_indivtablepress_options() {
        if( isset( $_POST['indivtablepress_nonce'] ) && wp_verify_nonce( $_POST['indivtablepress_nonce'], 'indivtablepress_nonce') ) {
            
            // sanitize the input
            $options = array( 'default' => '' );
            
            // save options
            if(isset( $_POST['options']['default'] ) ) $options['default']  = sanitize_key( $_POST['options']['default'] ) == 'on';
            update_option( 'indivtablepress_default', $options['default'] );
    
            // redirect the user back
            $admin_notice = 'success';
            inDivTablePress::redirect( 'admin.php', array( 'page' => 'indivtablepress', 'notice' => $admin_notice ) );
            exit;
        }			
        else {
            wp_die( __( 'Invalid nonce specified', 'inDivTablePress' ), __( 'Error', 'inDivTablePress' ), array(
            'response' 	=> 403,
            'back_link' => 'admin.php?page=' . 'indiv_tablepress',
            ) );
        }        
    }
    
    /**
     * Show an error notice to admins, if TablePress's minimum requirements are not reached.
     *
     * @since 1.0.2
     */
    public static function show_minimum_requirements_error_notice() {
        echo '<div class="notice notice-error form-invalid"><p>' .
            '<strong>Attention:</strong> ' .
            'The installed version of WordPress is too old for the inDiv for TablePress plugin<br /><strong>Please <a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">update your WordPress installation</a></strong>!' .
            "</p></div>\n";
    }

    /**
     * Redirect the user to another page
     *
     * @since 1.0.2
     */
    public static function redirect( $target, array $params = array() ) {
        $redirect = add_query_arg( $params, admin_url( $target ) );
        wp_redirect( $redirect );
        exit;
    }

} // class inDivTablePress




/*
 * Usage and possible parameters:
 * [table id=1 in_div=true /]
 *
 * in_div: Whether the table will be enclosed in a div.
 */

// Start up inDivTablePress on WordPress's "init" action hook.
add_action( 'tablepress_run', array( 'inDivTablePress', 'run' ) );
