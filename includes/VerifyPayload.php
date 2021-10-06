<?php
/**
 * Users are automatically logged-in with a dummy account,
 * the user's credentials are tested & if the username is correct but the password is incorrect,
 * the password is updated to match, then the credentials are re-tested.
 * If valid, the payload is returned w/ the user's username.
 *
 * @TODO
 * - If username is incorrect, the JWT should be revoked.
 * - Add Auth Code validation.
 *
 * @package bszyk-sso
 */

namespace BSZYK_SSO;

/**
 * JWTLogin
 */
class VerifyPayload {

	/**
	 * Init
	 */
	public function init() {
		add_filter( 'simple_jwt_login_jwt_payload_auth', array( $this, 'update_payload' ), 10, 2 );
	}

	/**
	 * Update JWT payload with user's username or flag to revoke token.
	 *
	 * @param array $payload JWT payload.
	 * @param array $request Request body.
	 *
	 * @return array $payload Updated payload.
	 */
	public function update_payload( $payload, $request ) {
		// user provided credentials.
		$username = $request['user_username'];
		$password = $request['user_password'];

		if ( true === $this->verify_user( $username, $password ) ) {
			$payload['username'] = $username;

			$user_id    = username_exists( $username );
			$user_info  = get_userdata( $user_id );
			$user_email = $user_info->user_email;

			$payload['email'] = $user_email;

			return $payload;
		} else {
			// invalidate token.
			exit();
		}
	}

	/**
	 * Verify that user exists and password is correct, update password if needed.
	 *
	 * @param string $username Provided username.
	 * @param string $password Provided password.
	 * @param bool   $first_try Whether or not password has already been updated.
	 */
	public function verify_user( $username, $password, $first_try = true ) {
		if ( false === username_exists( $username ) ) {
			return false;
		}

		$user = wp_authenticate_username_password( null, $username, $password );

		if ( is_wp_error( $user ) ) {
			// echo esc_html( $user->get_error_message() );
			if ( true === $first_try ) {
				$this->update_pw( $username, $password );
			} else {
				// something is wrong, avoid infinite loop.
				return false;
			}
		}

		// original or updated credentials are valid.
		return true;
	}

	/**
	 * Called if a username is valid but password is not,
	 * checks if user is a subscriber and updates with provided password.
	 *
	 * @param string $username Provided username.
	 * @param string $password Provided password.
	 */
	public function update_pw( $username, $password ) {
		$user_id = username_exists( $username );

		if ( true === $this->check_if_not_admin( $user_id ) ) {
			wp_set_password( $password, $user_id );
		} else {
			// @TODO handle error.
			exit();
		}

		$first_try = false;
		$this->verify_user( $username, $password, $first_try );
	}

	/**
	 * Check if new user is a subscriber role.
	 *
	 * @param int $user_id ID of newly registered user.
	 *
	 * @return bool
	 */
	public function check_if_not_admin( $user_id ) {
		$user       = get_userdata( $user_id );
		$user_roles = $user->roles;

		if ( ! in_array( 'admin', $user_roles, true ) ) {
			return true;
		} else {
			return false;
		}
	}
}
