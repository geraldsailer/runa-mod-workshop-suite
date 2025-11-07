<?php

declare(strict_types=1);

namespace Runa\WorkshopSuite\Auth;

/**
 * Renders the front-page login / registration experience directly from the plugin.
 */
final class LandingPage {
	private string $template;

	public function __construct(string $templatePath) {
		$this->template = $templatePath;
	}

	public function register(): void {
		add_filter('template_include', [$this, 'maybeLoadTemplate'], PHP_INT_MAX);
	}

	public function maybeLoadTemplate(string $template): string {
		if (! $this->shouldOverride()) {
			return $template;
		}

		return $this->template;
	}

	private function shouldOverride(): bool {
		if (is_admin() || is_customize_preview() || is_user_logged_in()) {
			return false;
		}

		if (! function_exists('is_front_page')) {
			return false;
		}

		/**
		 * Allows disabling the custom landing page override.
		 */
		$enabled = (bool) apply_filters('runa_wss/login/enable_landing_page', true);

		return $enabled && is_front_page();
	}
}
