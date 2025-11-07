<?php

declare(strict_types=1);

namespace Runa\WorkshopSuite\Auth;

/**
 * Prevents non-privileged frontend users from accessing wp-admin.
 */
final class AdminAccessGuard {
	private string $redirectUrl;

	public function __construct(string $redirectUrl) {
		$this->redirectUrl = $redirectUrl;
	}

	public function register(): void {
		add_action('admin_init', [$this, 'blockBackendAccess']);
		add_filter('login_redirect', [$this, 'forceFrontendRedirect'], 10, 3);
		add_filter('show_admin_bar', [$this, 'maybeHideAdminBar']);
	}

	public function blockBackendAccess(): void {
		if (! is_user_logged_in()) {
			return;
		}

		if ($this->currentUserCanAccessAdmin()) {
			return;
		}

		if (defined('DOING_AJAX') && DOING_AJAX) {
			return;
		}

		if (wp_doing_cron()) {
			return;
		}

		$target = $this->redirectUrl;

		if ($this->isSameDestination($target)) {
			$target = home_url('/');
		}

		wp_safe_redirect($target);
		exit;
	}

	/**
	 * @param string      $redirectTo Default redirect URL.
	 * @param string      $requested  Requested redirect URL.
	 * @param \WP_User|\WP_Error $user User instance or WP_Error on failure.
	 *
	 * @return string
	 */
	public function forceFrontendRedirect(string $redirectTo, string $requested, $user): string {
		if (! $user instanceof \WP_User) {
			return $redirectTo;
		}

		if ($this->currentUserCanAccessAdmin($user)) {
			return $redirectTo;
		}

		return $this->redirectUrl;
	}

	public function maybeHideAdminBar($show): bool {
		if (! is_user_logged_in()) {
			return $show;
		}

		if ($this->currentUserCanAccessAdmin()) {
			return $show;
		}

		return false;
	}

	private function currentUserCanAccessAdmin(?\WP_User $user = null): bool {
		$user = $user instanceof \WP_User ? $user : wp_get_current_user();

		if (! $user instanceof \WP_User) {
			return false;
		}

		$allowed = user_can($user, 'manage_options');

		/**
		 * Allows overriding who may access wp-admin without being redirected.
		 *
		 * @param bool     $allowed Whether the user may access the backend.
		 * @param \WP_User $user    User instance.
		 */
		return (bool) apply_filters('runa_wss/login/can_access_admin', $allowed, $user);
	}

	private function isSameDestination(string $target): bool {
		$requestUri = $_SERVER['REQUEST_URI'] ?? '';
		$currentPath = trim((string) wp_parse_url($requestUri, PHP_URL_PATH), '/');
		$targetPath  = trim((string) wp_parse_url($target, PHP_URL_PATH), '/');

		return $currentPath === $targetPath;
	}
}
