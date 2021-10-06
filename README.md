# Custom SSO Plugin for WordPress

`composer install`

This plugin currently has two functions:

## Service Provider 
If the `JwtLogin` class is enabled, it will allow you to login to another WP site (aka the Identity Provider aka  IdP) by:

1. Sending a request with credentials to the IdP's JWT endpoint (IdP needs JWT-Auth plugin installed).
2. If the request is successful, a token will be returned which will can be sent as an authorization header to another endpoint on the IdP.
3. With the token, username, and password, another request is sent to a custom endpoint which, if authorized, will trigger a login on the IdP.
4. If successful, you will be redirected & logged in to the admin site.

## Identity Provider
**JWT-Auth Plugin must be enabled**

If the `VerifyPayload` class is enabled, it will allow a request from another site to login to the IdP by:

1. JWT-Auth will create an endpoint that will return a token.
2. A custom REST endpoint is created.
3. If this custom endpoint receives a POST request with token authorization headers and credentials, it will sign that user in and send a 200 response.

---

**For JWT-Auth to work** this must be in your wp-config. Refer to plugin setup for more info.
```
define( 'JWT_AUTH_SECRET_KEY', 'secret-key' );
define( 'JWT_AUTH_CORS_ENABLE', true );
```