<?php
//Function to configure Mimi Captcha for Wordpress
function micaptcha_general_options() {
?>
<div class="wrap">
	<h1>Mimi Captcha</h1>
	<div class="notice notice-info is-dismissible">
		<p>
		<?php printf(__('<strong>Thank you for using Mimi Captcha. Remember to save changes manually after changing settings.</strong><br/>Visit our <a href="%1$s" target="_blank" rel="noopener noreferrer">GitHub Repo</a> for more infomation.', 'mimi-captcha'), 'https://github.com/stevenjoezhang/mimi-captcha'); ?>
		</p>
	</div>

<?php
	if (!current_user_can('manage_options')) return;
	//Display only for those who can actually deactivate plugins
	$mi_options = array(
		'type' => array('alphanumeric', 'alphabets', 'numbers', 'chinese', 'math'),
		'letters' => array('capital', 'small', 'capitalsmall'),
		'case_sensitive' => array('sensitive', 'insensitive'),
		'total_no_of_characters' => array('2', '3', '4', '5', '6'),
		'timeout_time' => array('30', '60', '120', '300', '600', '0'),
		'loading_mode' => array('default', 'onload', 'oninput'),
		'login' => array('yes', 'no'),
		'register' => array('yes', 'no'),
		'password' => array('yes', 'no'),
		'lost' => array('yes', 'no'),
		'comments' => array('yes', 'no'),
		'registered' => array('yes', 'no'),
		'whitelist_ips' => array()
		//'whitelist_usernames' => array()
	);
	if (isset($_POST['submit']) && check_admin_referer(plugin_basename(__FILE__), 'micaptcha_settings_nonce')) {
?>
	<div id="message" class="updated fade">
		<p>
			<strong><?php _e('Options saved.', 'mimi-captcha'); ?></strong>
		</p>
	</div>
<?php
		foreach ($mi_options as $mi_option => $mi_value) {
			if (isset($_POST[$mi_option])) {
				if (empty($mi_value)) {
					$data = (!empty($_POST[$mi_option])) ? explode("\n", str_replace("\r", "", stripslashes($_POST[$mi_option]))) : array();
					if (!empty($data)) {
						foreach ($data as $key => $ip) {
							if ('' == $ip) unset($data[$key]);
							else $data[$key] = sanitize_text_field($data[$key]);
						}
					}
					update_option('micaptcha_'.$mi_option, $data);
				}
				else if (in_array($_POST[$mi_option], $mi_value, true)) { //Validate POST calls
					$mi_index = array_search($_POST[$mi_option], $mi_value, true);
					if (isset($mi_value[$mi_index])) update_option('micaptcha_'.$mi_option, $mi_value[$mi_index]);
					//update_option() function receives $mi_value[$mi_index] as the second parameter, which is safe
				}
				else {
					update_option('micaptcha_'.$mi_option, $mi_value[0]);
				}
			}
		}
	}
	$mi_opt = array();
	foreach ($mi_options as $mi_option => $mi_value) {
		$mi_opt[$mi_option] = get_option('micaptcha_'.$mi_option);
	}
	$whitelist_ips = (is_array($mi_opt['whitelist_ips']) && !empty($mi_opt['whitelist_ips'])) ? implode("\n", $mi_opt['whitelist_ips']) : '';
	//$whitelist_usernames = (is_array($mi_opt['whitelist_usernames']) && !empty($mi_opt['whitelist_usernames'])) ? implode("\n", $mi_opt['whitelist_usernames']) : '';
?>
	<form method="post" action="" id="micaptcha">
		<?php wp_nonce_field(plugin_basename(__FILE__), 'micaptcha_settings_nonce');//?>
		<style>
			#micaptcha tr p {
				float: left;
				margin-right: 25px;
			}
		</style>
		<h2><?php _e('Configuration', 'mimi-captcha'); ?></h2>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Captcha type', 'mimi-captcha'); ?></th>
				<td>
					<select name="type">
						<option value="alphanumeric" <?php if ($mi_opt['type'] == 'alphanumeric') echo 'selected="selected"'; ?>><?php _e('Alphanumeric', 'mimi-captcha'); ?></option>
						<option value="alphabets" <?php if ($mi_opt['type'] == 'alphabets') echo 'selected="selected"'; ?>><?php _e('Alphabets', 'mimi-captcha'); ?></option>
						<option value="numbers" <?php if ($mi_opt['type'] == 'numbers') echo 'selected="selected"'; ?>><?php _e('Numbers', 'mimi-captcha'); ?></option>
						<option value="chinese" <?php if ($mi_opt['type'] == 'chinese') echo 'selected="selected"'; ?>><?php _e('Chinese chars', 'mimi-captcha'); ?></option>
						<option value="math" <?php if ($mi_opt['type'] == 'math') echo 'selected="selected"'; ?>><?php _e('Math Captcha', 'mimi-captcha'); ?></option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Captcha letters type', 'mimi-captcha'); ?></th>
				<td>
					<select name="letters">
						<option value="capital" <?php if ($mi_opt['letters'] == 'capital') echo 'selected="selected"'; ?>><?php _e('Capital letters only', 'mimi-captcha'); ?></option>
						<option value="small" <?php if ($mi_opt['letters'] == 'small') echo 'selected="selected"'; ?>><?php _e('Small letters only', 'mimi-captcha'); ?></option>
						<option value="capitalsmall" <?php if ($mi_opt['letters'] == 'capitalsmall') echo 'selected="selected"'; ?>><?php _e('Capital & small letters', 'mimi-captcha'); ?></option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Case sensitive', 'mimi-captcha'); ?></th>
				<td>
					<select name="case_sensitive">
						<option value="sensitive" <?php if ($mi_opt['case_sensitive'] == 'sensitive') echo 'selected="selected"'; ?>><?php _e('Sensitive', 'mimi-captcha'); ?></option>
						<option value="insensitive" <?php if ($mi_opt['case_sensitive'] == 'insensitive') echo 'selected="selected"'; ?>><?php _e('Insensitive', 'mimi-captcha'); ?></option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Total number of Captcha characters', 'mimi-captcha'); ?></th>
				<td>
					<select name="total_no_of_characters">
					<?php 
						for ($i = 2; $i <= 6; $i++) {
							print '<option value="'.$i.'" ';
							if ($mi_opt['total_no_of_characters'] == $i) echo 'selected="selected"';
							print '>'.$i.'</option>';
						}
					?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Captcha expiration time', 'mimi-captcha'); ?></th>
				<td>
					<select name="timeout_time">
						<option value="30" <?php if ($mi_opt['timeout_time'] == 30) echo 'selected="selected"'; ?>><?php _e('30 seconds', 'mimi-captcha'); ?></option>
						<option value="60" <?php if ($mi_opt['timeout_time'] == 60) echo 'selected="selected"'; ?>><?php _e('1 min', 'mimi-captcha'); ?></option>
						<option value="120" <?php if ($mi_opt['timeout_time'] == 120) echo 'selected="selected"'; ?>><?php _e('2 min', 'mimi-captcha'); ?></option>
						<option value="300" <?php if ($mi_opt['timeout_time'] == 300) echo 'selected="selected"'; ?>><?php _e('5 min', 'mimi-captcha'); ?></option>
						<option value="600" <?php if ($mi_opt['timeout_time'] == 600) echo 'selected="selected"'; ?>><?php _e('10 min', 'mimi-captcha'); ?></option>
						<option value="0" <?php if ($mi_opt['timeout_time'] == 0) echo 'selected="selected"'; ?>><?php _e('Unlimited', 'mimi-captcha'); ?></option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Captcha loading mode', 'mimi-captcha'); ?></th>
				<td>
					<select name="loading_mode">
						<option value="default" <?php if ($mi_opt['loading_mode'] == 'default') echo 'selected="selected"'; ?>><?php _e('Default', 'mimi-captcha'); ?></option>
						<option value="onload" <?php if ($mi_opt['loading_mode'] == 'onload') echo 'selected="selected"'; ?>><?php _e('On page load', 'mimi-captcha'); ?></option>
						<option value="oninput" <?php if ($mi_opt['loading_mode'] == 'oninput') echo 'selected="selected"'; ?>><?php _e('On user input', 'mimi-captcha'); ?></option>
					</select>
				</td>
			</tr>
		</table>

		<h2><?php _e('Captcha display Options', 'mimi-captcha'); ?></h2>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Login form', 'mimi-captcha'); ?></th>
				<td>
					<p>
						<label>
							<input type="radio" name="login" value="yes" <?php if ($mi_opt['login'] == 'yes') echo 'checked="checked"'; ?>/><?php _e('Enable', 'mimi-captcha'); ?>
						</label>
					</p>
					<p>
						<label><input type="radio" name="login" value="no" <?php if ($mi_opt['login'] === false || $mi_opt['login'] == 'no') echo 'checked="checked"'; ?>/><?php _e('Disable', 'mimi-captcha'); ?>
						</label>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Register form', 'mimi-captcha'); ?></th>
				<td>
					<p>
						<label>
							<input type="radio" name="register" value="yes" <?php if ($mi_opt['register'] == 'yes') echo 'checked="checked"'; ?>/><?php _e('Enable', 'mimi-captcha'); ?>
						</label>
					</p>
					<p>
						<label><input type="radio" name="register" value="no" <?php if ($mi_opt['register'] === false || $mi_opt['register'] == 'no') echo 'checked="checked"'; ?>/><?php _e('Disable', 'mimi-captcha'); ?>
						</label>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Allow new users to enter a password', 'mimi-captcha'); ?></th>
				<td>
					<p>
						<label>
							<input type="radio" name="password" value="yes" <?php if ($mi_opt['password'] == 'yes') echo 'checked="checked"'; ?>/><?php _e('Yes', 'mimi-captcha'); ?>
						</label>
					</p>
					<p>
						<label><input type="radio" name="password" value="no" <?php if ($mi_opt['password'] === false || $mi_opt['password'] == 'no') echo 'checked="checked"'; ?>/><?php _e('No', 'mimi-captcha'); ?>
						</label>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Lost password form', 'mimi-captcha'); ?></th>
				<td>
					<p>
						<label>
							<input type="radio" name="lost" value="yes" <?php if ($mi_opt['lost'] == 'yes') echo 'checked="checked"'; ?>/><?php _e('Enable', 'mimi-captcha'); ?>
						</label>
					</p>
					<p>
						<label><input type="radio" name="lost" value="no" <?php if ($mi_opt['lost'] === false || $mi_opt['lost'] == 'no') echo 'checked="checked"'; ?>/><?php _e('Disable', 'mimi-captcha'); ?>
						</label>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Comments form', 'mimi-captcha'); ?></th>
				<td>
					<p>
						<label>
							<input type="radio" name="comments" value="yes" <?php if ($mi_opt['comments'] == 'yes') echo 'checked="checked"'; ?>/><?php _e('Enable', 'mimi-captcha'); ?>
						</label>
					</p>
					<p>
						<label><input type="radio" name="comments" value="no" <?php if ($mi_opt['comments'] === false || $mi_opt['comments'] == 'no') echo 'checked="checked"'; ?>/><?php _e('Disable', 'mimi-captcha'); ?>
						</label>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Hide Captcha for logged in users', 'mimi-captcha'); ?></th>
				<td>
					<p>
						<label>
							<input type="radio" name="registered" value="yes" <?php if ($mi_opt['registered'] === false || $mi_opt['registered'] == 'yes') echo 'checked="checked"'; ?>/><?php _e('Yes', 'mimi-captcha'); ?>
						</label>
					</p>
					<p>
						<label><input type="radio" name="registered" value="no" <?php if ($mi_opt['registered'] == 'no') echo 'checked="checked"'; ?>/><?php _e('No', 'mimi-captcha'); ?>
						</label>
					</p>
				</td>
			</tr>
		</table>

		<h2><?php _e('Captcha fonts', 'mimi-captcha'); ?></h2>
		<p><?php _e('You can upload fonts (.ttf) to /wp-content/plugins/mimi-captcha/fonts folder. Fonts will be chosen randomly when generating Captcha.', 'mimi-captcha'); ?></p>

		<h2><?php _e('Whitelist', 'mimi-captcha'); ?></h2>
		<div>
			<p><?php _e('One IP or IP range (1.2.3.4-5.6.7.8) per line.', 'mimi-captcha'); ?></p>
			<textarea name="whitelist_ips" rows="10" cols="50"><?php echo esc_textarea($whitelist_ips); ?></textarea>
		</div>

		<h2><?php _e('Blacklist', 'mimi-captcha'); ?></h2>
		<p><?php _e('Coming soon...', 'mimi-captcha'); ?></p>

		<?php submit_button(); ?>
	</form>
</div>
<?php
}
?>
