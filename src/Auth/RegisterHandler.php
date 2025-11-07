<?php

declare(strict_types=1);

namespace Runa\WorkshopSuite\Auth;

/**
 * Handles classic email/password registrations from the landing page.
 */
final class RegisterHandler {
	public const ACTION = 'runa_wss_register';

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
			wp_safe_redirect(add_query_arg(['registration' => 'invalid'], $redirect));
			exit;
		}

		$name = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
		$email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
		$password = (string) ($_POST['password'] ?? '');

		if (empty($name) || empty($email) || empty($password)) {
			wp_safe_redirect(add_query_arg(['registration' => 'missing'], $redirect));
			exit;
		}

		if (! is_email($email)) {
			wp_safe_redirect(add_query_arg(['registration' => 'email'], $redirect));
			exit;
		}

		if (email_exists($email)) {
			wp_safe_redirect(add_query_arg(['registration' => 'exists'], $redirect));
			exit;
		}

		$username = $this->generateUsername($email);

		$user_id = wp_create_user($username, $password, $email);

		if (is_wp_error($user_id)) {
			wp_safe_redirect(add_query_arg(['registration' => 'error'], $redirect));
			exit;
		}

		wp_update_user([
			'ID'           => $user_id,
			'display_name' => $name,
		]);

		$user = get_user_by('id', $user_id);

		if ($user instanceof \WP_User) {
			$user->set_role(Roles::ROLE_PARTICIPANT);
		}

		wp_safe_redirect(add_query_arg(['registration' => 'success'], $redirect));
		exit;
	}

	private function determineRedirect(): string {
		if (! empty($_POST['redirect_to'])) {
			$redirect = esc_url_raw(wp_unslash($_POST['redirect_to']));
		} else {
			$redirect = home_url('/');
		}

		return $redirect;
	}

	private function isValidRequest(): bool {
		return isset($_POST['_runa_wss_register_nonce'])
			&& wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_runa_wss_register_nonce'])), 'runa_wss_register');
	}

	private function generateUsername(string $email): string {
		$base = sanitize_user(current(explode('@', $email, 2)));

		if (empty($base)) {
			$base = 'user';
		}

		$username = $base;
		$counter = 1;

		while (username_exists($username)) {
			$username = sprintf('%s%d', $base, $counter);
			++$counter;
		}

		return $username;
	}
}
