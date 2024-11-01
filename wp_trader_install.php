<?php
/**
* @package WP-Trader
*/
/*
* Plugin Name: WP-Trader
* Plugin URI: http://wp-tracker.com/
* Description: Roughly based on Torrent Trader 2.07, WP-Trader is an easy solution for people to run a torrent site. Now it is easier for bands, software makers, authors and etc. to be able to get their work out to their users without the high cost of having servers for product download. We hope this plugin will be useful for people who do not want to distribute their works through the normal channel which like to act like a mafiaa and not pay the artists what they deserve (we will not mention the companies names but you should know who you are and should not fear us but embrace us). Users of this plugin should only use it to distribute work which they own the rights to. The author(s) of this plugin can not be held responsible for the use of this plugin.
* Version: .4.9 Beta
* Author: Andrew Walker & Lee Howarth
* Author URI: http://wp-tracker.com/
* Lisence: GPLv2
*/ 
require_once( dirname( __FILE__ ) . "/wp_trader_install_defines.php" );
require_once( WP_TRADER_ABSPATH . "/includes/required-includes.php" );
	
global $wp_trader_plugin_description, $wp_trader_plugin_version, $wp_trader_plugin_author;
if ( ! function_exists( "get_plugin_data" ) )
	require_once( ABSPATH . "wp-admin/includes/plugin.php" );
$wp_trader_plugin_data = get_plugin_data( __FILE__, true, true );
$wp_trader_plugin_description = $wp_trader_plugin_data["Description"];
$wp_trader_plugin_version = $wp_trader_plugin_data["Version"];
$wp_trader_plugin_author = $wp_trader_plugin_data["Author"];
	
global $wp_trader_db_version;
$wp_trader_db_version = "1.1";

if( ! function_exists("wptrader_install") ) {
	function wptrader_install(){
		require_once( WP_TRADER_ABSPATH . "/includes/function-main.php" );
		require_once( WP_TRADER_ABSPATH . "/wp_trader_install_functions.php" );
	}
}

if( ! function_exists("wptrader_uninstall") ) {
	function wptrader_uninstall(){
		require_once( WP_TRADER_ABSPATH . "/includes/function-main.php" );
		if(get_option("wp_trader_keep_settings") == 0){
			require_once( WP_TRADER_ABSPATH . "/wp_trader_uninstall_functions.php" );
			global $wpdb;
			if ( function_exists( "wp_trader_uninstall_options" ) )
				wp_trader_uninstall_options(); //need to add an option to keep
			delete_option("wp_trader_keep_settings");
		}
		if(get_option("wp_trader_keep_settings") == 0 || get_option("wp_trader_keep_posts") == 0){
			require_once( WP_TRADER_ABSPATH . "/wp_trader_uninstall_functions.php" );
			$query = "SELECT post_id, attachment_id FROM " . TRADER_TORRENTS . "";
			$res = mysql_query($query) or die(mysql_error());
			while ($row = mysql_fetch_assoc($res)) {
				wp_delete_post( $row["post_id"], true );
				wp_delete_post( $row["attachment_id"], true );
				delete_post_meta($row["post_id"], "_thumbnail_id");
				delete_post_meta($row["post_id"], "anon");
				delete_post_meta($row["post_id"], "freeleech");
				delete_post_meta($row["post_id"], "external");
				delete_post_meta($row["post_id"], "views");
				delete_post_meta($row["post_id"], "hits");
				delete_post_meta($row["post_id"], "times_completed");
				delete_post_meta($row["post_id"], "leechers");
				delete_post_meta($row["post_id"], "seeders");
				delete_post_meta($row["post_id"], "torrent_location");
				delete_post_meta($row["post_id"], "last_action");
				delete_post_meta($row["post_id"], "descr");
			}
			delete_option("wp_trader_keep_posts");
		}
		if(get_option("wp_trader_keep_settings") == 0 || get_option("wp_trader_keep_all_pages") == 0){
			require_once( WP_TRADER_ABSPATH . "/wp_trader_uninstall_functions.php" );
			if ( function_exists( "wp_trader_uninstall_pages" ) )
				wp_trader_uninstall_pages();
			delete_option("wp_trader_keep_all_pages");
		}
		if(get_option("wp_trader_keep_settings") == 0 || get_option("wp_trader_keep_all_user_info") == 0){
			require_once( WP_TRADER_ABSPATH . "/wp_trader_uninstall_functions.php" );
			if ( function_exists( "wp_trader_uninstall_user_meta" ) )
				wp_trader_uninstall_user_meta();
			delete_option("wp_trader_keep_all_user_info");
		}
		if(get_option("wp_trader_keep_settings") == 0 || get_option("wp_trader_keep_system_user") == 0){
			require_once( WP_TRADER_ABSPATH . "/wp_trader_uninstall_functions.php" );
			if ( function_exists( "wp_trader_uninstall_system_user" ) )
				wp_trader_uninstall_system_user();
			delete_option("wp_trader_keep_system_user");
		}
		if(get_option("wp_trader_keep_settings") == 0 || get_option("wp_trader_keep_databank_tables") == 0){
			require_once( WP_TRADER_ABSPATH . "/wp_trader_uninstall_functions.php" );
			if ( function_exists( "wp_trader_uninstall_db" ) )
				wp_trader_uninstall_db();
			delete_option("wp_trader_keep_databank_tables");
		}
	}
}

register_activation_hook(__FILE__, "wptrader_install");
register_deactivation_hook(__FILE__, "wptrader_uninstall");

add_action("wp_head", "wp_trader_head");
if( ! function_exists("wp_trader_head") ) {
	function wp_trader_head(){
		if (!is_admin()){
			?>  
			<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/themes/base/jquery-ui.css">
			<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
			<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
			<?php
		}
	}
}

if (get_option("members_only") == 1){
	require_once( WP_TRADER_ABSPATH . "/includes/function-members-only.php" );
}

add_action( 'show_user_profile', 'wptrader_custom_user_profile_fields' );
add_action( 'edit_user_profile', 'wptrader_custom_user_profile_fields' );
add_action( 'personal_options_update', 'wptrader_save_custom_user_profile_fields' );
add_action( 'edit_user_profile_update', 'wptrader_save_custom_user_profile_fields' );

// Add cron job to run cleanup
/*function wptrader_cron_schedule( $schedules ) {
    $schedules['weekly'] = array(
        'interval' => 604800, // 1 week in seconds
        'display'  => __( 'Once Weekly' ),
    );
 
    return $schedules;
}*/
 
// Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'wptrader_cron_action' ) ) {
    wp_schedule_event( time(), 'hourly', 'wptrader_cron_action', wptrader_cron_schedule() );
}
 
// Hook into that action that'll fire weekly
add_action( 'wptrader_cron_action', 'wptrader_cron_do_cleanup' );
function wptrader_cron_do_cleanup() {
    require_once( WP_TRADER_ABSPATH . "/admin/function-cleanup.php" );
	do_cleanup();
}
add_filter( 'cron_schedules', 'wptrader_cron_schedule' );
function wptrader_cron_schedule() {
	return array( 'wptrader_cleanup_time' => array(
		'interval' => get_option('cleanup_autoclean_interval'), // seconds
		'display' => __( 'WP-Trader Cleanup' )
	) );
}
?>