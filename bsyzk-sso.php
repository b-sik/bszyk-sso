<?php
/**
 * EA SSO WordPress Plugin
 *
 * @package bszyk-sso
 *
 * Plugin Name: Custom SSO for WordPress
 * Plugin URI: https://bszyk.dev
 * Description: Custom SSO plugin for WordPress utilizing Simple-JWT-Login
 * Author: Brian Siklinski
 * Version: 0.1.0
 * Author URI: https://bszyk.dev
 */

namespace BSZYK_SSO;

require_once __DIR__ . '/vendor/autoload.php';

// enable on identity provider.
use BSZYK_SSO\JwtLogin;

// enable on target site.
use BSZYK_SSO\VerifyPayload;

/**
 * Create new instance of plugin.
 */
class BSZYK_SSO {

	/**
	 * Construct.
	 */
	public function __construct() {
		$jwt_login = new JwtLogin();
		$jwt_login->init();

		// $verify_payload = new VerifyPayload();
		// $verify_payload->init();
	}
}

new BSZYK_SSO();
