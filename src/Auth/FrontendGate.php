<?php

declare(strict_types=1);

namespace Runa\WorkshopSuite\Auth;

/**
 * Forces unauthenticated visitors onto the login landing page.
 */
final class FrontendGate {
	private string $loginUrl;

	public function __construct(string $loginUrl) {
		$this->loginUrl = $loginUrl;
	}

	public function register(): void {
		add_action('template_redirect', [$this, 'maybeRedirectGuest']);
	}

	public function maybeRedirectGuest(): void {
		if (is_user_logged_in()) {
			return;
		}

		if (is_admin() || wp_doing_ajax()) {
			return;
		}

		if ($this->isCoreAuthRoute()) {
			return;
		}

		if ($this->isAllowedRequest()) {
			return;
		}

		wp_safe_redirect($this->loginUrl);
		exit;
	}

	private function isAllowedRequest(): bool {
		if (function_exists('is_front_page') && is_front_page()) {
			return true;
		}

		$allowedPaths = apply_filters('runa_wss/login/public_paths', []);

		if (empty($allowedPaths)) {
			return false;
		}

		$requestPath = trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

		foreach ($allowedPaths as $path) {
			$path = trim((string) $path, '/');

			if ($path === '') {
				continue;
			}

			if (0 === strpos($requestPath, $path)) {
				return true;
			}
		}

		return false;
	}

	private function isCoreAuthRoute(): bool {
		$path = trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

		if ($path === '') {
			return false;
		}

		$coreRoutes = [
			'wp-login.php',
			'wp-login',
			'wp-signup.php',
			'wp-activate.php',
		];

		foreach ($coreRoutes as $route) {
			if (stripos($path, $route) !== false) {
				return true;
			}
		}

		return false;
	}
}
