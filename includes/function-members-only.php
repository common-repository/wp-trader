<?php
/*
  * Holds the Members Only
  * @package WP-Trader
  * @subpackage Template
*/
//credits and some part of the memebrs only redirect go to http://wordpress.org/extend/plugins/registered-users-only/
add_action( "wp", "members_only_redirect" );
if( ! function_exists("members_only_redirect") ) {
	function members_only_redirect() {
		global $post;
		// If the user is logged in, then abort
		if ( current_user_can('read') ) return;

		$members_only = get_option("members_only");

		// Feeds
		if ( 1 == $members_only["feeds"] && is_feed() ) return;

		// This is a base array of pages that will be EXCLUDED from being blocked
		$page_exclude = array_map("trim", @explode("|", get_option("members_only_page_exclude")));

		// If the current script name is in the exclusion list, abort
		if ( in_array( basename(get_permalink()), apply_filters( "wp_trader_memebers_only", $page_exclude ) ) ) return;

		// Still here? Okay, then redirect to the login form
		auth_redirect();
	}
}

add_action( "init", "wp_trader_login_form_message" );
if( ! function_exists("wp_trader_login_form_message") ) {
	function wp_trader_login_form_message() {
		// Don't show the error message if anything else is going on (registration, etc.)
		if ( "wp-login.php" != basename($_SERVER["PHP_SELF"]) || !empty($_POST) || ( !empty($_GET) && empty($_GET["redirect_to"]) ) ) return;
			global $error;
			$error = __("Only registered and logged in users are allowed to view this site. Please log in now.", "registered-users-only");
	}
		
	// Tell bots to go away (they shouldn't index the login form)
	add_action( "login_head", "wp_trader_no_index", 1 );
	function wp_trader_no_index(){
		return "	<meta name='robots' content='noindex,nofollow' />\n";
	}
}
?>