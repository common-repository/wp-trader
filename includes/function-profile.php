<?php
/*
  * Holds the Profile Functions
  * @package WP-Trader
  * @subpackage Template
*/

if( ! function_exists("wptrader_custom_user_profile_fields") ) {
	function wptrader_custom_user_profile_fields( $user ) {
		global $wpdb;
		require_once( WP_TRADER_ABSPATH . "/includes/function-main.php" );
	?>
		<h3><?php _e('Torrent Site Info', 'your_textdomain'); ?></h3>
		<table class="form-table">
			<tr>
				<th>
					<label for="trader_upload"><?php _e('Upload', 'your_textdomain'); ?></label>
				</th>
				<td>
					<?php 
						if ( !get_the_author_meta( 'trader_upload', $user->ID ) ){
							$user_upload = '0';
						}else{
							$user_upload = ''.get_the_author_meta( 'trader_upload', $user->ID ).'';
						}
						if( !current_user_can('level_10') ){
							echo esc_attr( mksize( $user_upload ) ); ?><br />
					<?php 
						}else{ 
					?>
							<input type="text" size="30" name="trader_upload" id="trader_upload" value="<?php echo esc_attr( $user_upload ); ?>" />&nbsp;&nbsp;<?php echo esc_attr( mksize( $user_upload ) ); ?><br />
							<span class="description">Value should be entered in bytes.</span><br />
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th>
					<label for="trader_download"><?php _e('Download', 'your_textdomain'); ?></label>
				</th>
				<td>
					<?php 
						if ( !get_the_author_meta( 'trader_download', $user->ID ) ){
							$user_download = '0';
						}else{
							$user_download = ''.get_the_author_meta( 'trader_download', $user->ID ).'';
						}
						if( !current_user_can('level_10') ){
							echo esc_attr( mksize( $user_download ) ); ?><br />
					<?php 
						}else{ 
					?>
						<input type="text" size="30" name="trader_download" id="trader_download" value="<?php echo esc_attr( $user_download ); ?>" />&nbsp;&nbsp;<?php echo esc_attr( mksize( $user_download ) ); ?><br />
						<span class="description">Value should be entered in bytes.</span><br />
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th>
					<label for="trader_ratio"><?php _e('Ratio', 'your_textdomain'); ?></label>
				</th>
				<td>
					<?php
						if (get_the_author_meta("trader_upload", $user->ID) > 0 && get_the_author_meta("trader_download", $user->ID) == 0){
							$user_ratio .= "Inf.";
						}elseif (get_the_author_meta("trader_download", $user->ID) > 0){
							$user_ratio .= number_format(get_the_author_meta("trader_upload", $user->ID) / get_the_author_meta("trader_download", $user->ID), 2);
						}else{
							$user_ratio .= "---";
						}
						echo esc_attr( $user_ratio ); ?><br />
				</td>
			</tr>
		
			<?php if( get_option('members_only') == 1 && get_option('wptrader_seed_bonus') == 1 ){ ?>
			<tr>
				<th>
					<label for="trader_ratio"><?php _e('Seed Bonus', 'your_textdomain'); ?></label>
				</th>
				<td>
					<?php 
						if ( !get_the_author_meta( 'seed_bonus', $user->ID ) ){
							$user_seed_bonus = '0';
						}else{
							$user_seed_bonus = ''.number_format(get_the_author_meta( 'seed_bonus', $user->ID ), 2).'';
						}
						if( !current_user_can('level_10') ){
							echo esc_attr( $user_seed_bonus ); ?><br />
					<?php 
						}else{ 
					?>
						<input type="text" size="10" name="seed_bonus" id="seed_bonus" value="<?php echo esc_attr( $user_seed_bonus ); ?>" /><br />
						<span class="description">Edit user's seed bonus amount.</span><br />
					<?php } ?>
				</td>
			</tr>
			<?php } ?>
			<?php if( get_option('members_only') == 1 && get_option('ip_passkey_tracking') == 1 ){ ?>
				<tr>
					<th>
						<label for="trader_ratio"><?php _e('Passkey', 'your_textdomain'); ?></label>
					</th>
					<td>
						<?php
							if(!get_the_author_meta("trader_passkey", $user->ID)){
								echo 'No passkey has been assigned for this account. Please download a torrent to assign one.';
							}else{
								echo esc_attr( get_the_author_meta("trader_passkey", $user->ID) ).'<br />';
							}
						?>
					</td>
					</tr>
				<?php if((current_user_can( 'edit_user', $user_id ) && get_option('user_generate_passkey') == 1) || current_user_can('level_10')){ ?>
					<tr>
						<th>
							<label for="reset_passkey"><?php _e('Passkey Reset', 'your_textdomain'); ?></label>
						</th>
						<td>
							<input name="reset_passkey" value="1" type="checkbox">&nbsp;&nbsp;Yes<br />
							<span class="description">Generate a new passkey for this account. (Any active torrents must be downloaded again to continue leeching or seeding.)</span><br />
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
			<?php 
				if( get_option('members_only') == 1 && current_user_can('level_10')){ 
					$download_banned_yes = (get_option('download_banned') == 1) ? 'checked="checked"' : '';
					$download_banned_no = (get_option('download_banned') == 0) ? 'checked="checked"' : '';
			?>
					<tr>
						<th>
							<label for="download_banned"><?php _e('Download Banned', 'your_textdomain'); ?></label>
						</th>
						<td>
							<input type="radio" name="download_banned" value="0" <?php echo $download_banned_no ?> />&nbsp;No&nbsp;&nbsp;<input type="radio" name="download_banned" value="1" <?php echo $download_banned_yes ?> />&nbsp;Yes<br />
							<span class="description">Ban the user from downloading.</span><br />
						</td>
					</tr>
			<?php } ?>
		</table>
<?php 
	}
}
if( ! function_exists("wptrader_save_custom_user_profile_fields") ) {
	function wptrader_save_custom_user_profile_fields( $user_id ) {
		if ( !current_user_can( 'edit_user', $user_id ) )
			return FALSE;
		update_usermeta( $user_id, 'trader_upload', $_POST['trader_upload'] );
		update_usermeta( $user_id, 'trader_download', $_POST['trader_download'] );
		update_usermeta( $user_id, 'download_banned', $_POST['download_banned'] );
		if($_POST['reset_passkey'] == "1"){
			$trader_secret = get_user_meta($user_id, 'trader_secret', true);
			$rand = array_sum(explode(" ", microtime()));
			$trader_passkey = md5($current_user->user_login.$rand.$trader_secret.($rand*mt_rand()));
			update_usermeta( $user_id, 'trader_passkey', $trader_passkey );
		}
	}
}
?>