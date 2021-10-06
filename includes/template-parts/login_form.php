<?php
/**
 * Login form
 *
 * @package bszyk-sso
 */
?>

<form action='<?php echo esc_attr( get_permalink() ); ?>' method='POST' style='border: 1px solid black; padding: 25px; margin: 25px;'>
	<?php wp_nonce_field( 'sso_user', 'sso_user_nonce' ); ?>
<h3>Login to the identity provider:</h3>
<label for='sso_username'> Enter a username:
	<input type='text' placeholder='username' name='sso_username' id='sso_username' />
</label>
<br />
<label for='sso_password'> Enter a password:
	<input type='text' placeholder='password' name='sso_password' id='sso_password' />
</label>
<br />
<button type='submit' value='Submit'>Submit</button>
<input type="hidden" name="action" value="get_credentials_from_form">
</form>
