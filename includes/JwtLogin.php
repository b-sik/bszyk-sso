<?php
/**
 * Login to another WP site utilizing Simple-JWT-Login plugin.
 *
 * @package bszyk-sso
 */

namespace BSZYK_SSO;

/**
 * JWTLogin
 */
class JwtLogin {

	/**
	 *
	 * Root url to request token from.
	 *
	 * @var string
	 */
	private $url;

	/**
	 *
	 * Namespace of REST route on target site.
	 *
	 * @var string
	 */
	private $rest_namespace;

	/**
	 * Init
	 */
	public function init() {
		// @TODO - make this a configuration
		$this->url            = 'http://localhost:8000';
		$this->rest_namespace = 'bszyk-sso';

		// @TODO add no change password filter.
		add_shortcode( 'login_form', array( $this, 'render_login_form' ) );
		add_action( 'init', array( $this, 'get_credentials_from_form' ) );
	}

	/**
	 * Send request to get JWT token and redirect to target site.
	 *
	 * @return void
	 */
	public function get_credentials_from_form() {
		if ( ! empty( $_POST ) || ! isset( $_POST['sso_user_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sso_user_nonce'] ) ), 'sso_user' ) ) {

			if ( isset( $_POST['sso_username'] ) && isset( $_POST['sso_password'] ) ) {
				$username = sanitize_text_field( wp_unslash( $_POST['sso_username'] ) );
				$password = sanitize_text_field( wp_unslash( $_POST['sso_password'] ) );

				$this->request_token( $username, $password );
			}
		}
	}

	/**
	 * Send request to get JWT token and redirect to target site.
	 *
	 * @param string $username Username.
	 * @param string $password Password.
	 * @return void
	 */
	public function request_token( $username, $password ) {
		$response = wp_remote_request(
			$this->url . '/wp-json/' . $this->rest_namespace . '/v1/auth',
			array(
				'method' => 'POST',
				'body'   => array(
					'user_username' => $username,
					'user_password' => $password,
					'username'     => 'test_admin',
					'password'     => '78%vDLo&yROlnMXtRK6HdVU)',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			echo esc_html( $response->get_error_message() );
			wp_die();
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $body->success ) {
			$token = $body->data->jwt;
			$this->login_to_target( $token );
		} else {
			echo esc_html( $body->data->message );
		}
	}

	/**
	 * Login and redirect to target site.
	 *
	 * @param string $token JWT.
	 * @return void
	 */
	public function login_to_target( $token ) {
		//phpcs:ignore
		wp_redirect( $this->url . '/wp-json/' . $this->rest_namespace . '/v1/autologin?jwt=' . $token );
		exit();
	}

	/**
	 * Render login form.
	 */
	public function render_login_form() {
		ob_start();
		include __DIR__ . '/template-parts/login_form.php';
		return ob_flush();
	}
}
