<?php

declare(strict_types=1);

namespace Runa\WorkshopSuite\Auth;

/**
 * Authenticates users via the front-page email login form.
 */
final class LoginHandler {
	public const ACTION = 'runa_wss_login';

	public function register(): void {
		add_action('init', [$this, 'maybeInterceptEarly']);
		add_action('admin_post_nopriv_' . self::ACTION, [$this, 'handle']);
		add_action('admin_post_' . self::ACTION, [$this, 'handle']);
	}

	public function maybeInterceptEarly(): void {
		if ('POST' !== ($_SERVER['REQUEST_METHOD'] ?? '')) {
			return;
		}

		$action = isset($_REQUEST['action']) ? sanitize_key((string) wp_unslash($_REQUEST['action'])) : '';

		if (self::ACTION !== $action) {
			return;
		}

		$this->handle();
	}

	public function handle(): void {
		$redirect = $this->determineRedirect();

		if (! $this->isValidRequest()) {
			$this->redirect(add_query_arg(['login' => 'invalid'], $redirect));
		}

		$email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
		$password = (string) ($_POST['password'] ?? '');

		if (empty($email) || empty($password) || ! is_email($email)) {
			$this->redirect(add_query_arg(['login' => 'missing'], $redirect));
		}

		$user = get_user_by('email', $email);

		if (! $user) {
			$this->redirect(add_query_arg(['login' => 'not_found'], $redirect));
		}

		$credentials = [
			'user_login'    => $user->user_login,
			'user_password' => $password,
			'remember'      => true,
		];

		$result = wp_signon($credentials, false);

		if (is_wp_error($result)) {
			$this->redirect(add_query_arg(['login' => 'failed'], $redirect));
		}

		$this->redirect($redirect);
	}

	private function determineRedirect(): string {
		if (! empty($_POST['redirect_to'])) {
			$redirect = esc_url_raw(wp_unslash($_POST['redirect_to']));
		} else {
			$redirect = self::defaultRedirectUrl();
		}

		return $redirect;
	}

	private function isValidRequest(): bool {
		return isset($_POST['_runa_wss_login_nonce'])
			&& wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_runa_wss_login_nonce'])), 'runa_wss_login');
	}

	public static function defaultRedirectUrl(): string {
		$front_page_id = (int) get_option('page_on_front');
		$url = '';

		if ($front_page_id > 0) {
			$url = get_permalink($front_page_id);
		}

		if (! $url) {
			$url = home_url('/dashboard/');
		}

		return (string) apply_filters('runa_wss/login/default_redirect', $url);
	}

	private function redirect(string $url): void {
		$location = esc_url_raw($url);
		$redirected = wp_safe_redirect($location);

		if (! $redirected) {
			$this->forceRedirect($location);
		}

		exit;
	}

	private function forceRedirect(string $location): void {
		$target = $location;
		$parsed = wp_parse_url($location);
		$host = $parsed['host'] ?? '';
		$site_host = wp_parse_url(home_url(), PHP_URL_HOST);

		if ($host && $site_host && strtolower($host) !== strtolower($site_host)) {
			$target = home_url('/');
		}

		header('Location: ' . $target, true, 302);
	}
}
